<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\services\TokenService;
use app\middleware\AuthMiddleware;
use app\models\User;
use app\models\Token;

class AuthController extends Controller
{
    private User $user;
    private Token $token;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->registerMiddleware(new AuthMiddleware(['logout']));
    }

    public function index()
    {
        $this->setLayout('auth');
        echo $this->render('auth', [
            'title' => 'The Daily News - Sign In'
        ]);
    }

    public function register(Request $request, Response $response)
    {
        $body = $request->getBody();
        if (empty($body['fullname']) || empty($body['email']) || empty($body['password'])) {
            $response->json(['error' => 'Missing required fields: fullname, email, password'], 400);
        }

        $roleId = $this->user->getRoleIdByName('Reader');

        if (!$roleId) {
            $response->json(['error' => 'Default role not found. Ensure migrations/seeders have run.'], 500);
        }

        $existingUser = $this->user->findByEmail($body['email']);
        if ($existingUser) {
            $response->json(['error' => 'Email already exists'], 400);
        }

        $username = explode('@', $body['email'])[0] . random_int(1000, 9999);
        $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);

        if ($this->user->create($username, $body['email'], $body['fullname'], $passwordHash, $roleId)) {
            $response->json(['success' => true, 'message' => 'Registration successful! You may now log in.']);
        }

        $response->json(['error' => 'Registration failed due to server error'], 500);
    }

    public function login(Request $request, Response $response)
    {
        $body = $request->getBody();
        if (empty($body['email']) || empty($body['password'])) {
            $response->json(['error' => 'Missing required fields: email, password'], 400);
        }

        $user = $this->user->findByEmail($body['email']);

        if ($user && password_verify($body['password'], $user['password'])) {
            $tokenStr = TokenService::generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            $this->token->create($user['id'], $tokenStr, $expiresAt);

            $response->json([
                'success' => true,
                'token' => $tokenStr,
                'message' => 'Login successful'
            ]);
        }

        $response->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request, Response $response)
    {
        $bearerToken = $request->getBearerToken();
        if ($bearerToken) {
            $this->token->deleteByToken($bearerToken);
        }
        $response->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
