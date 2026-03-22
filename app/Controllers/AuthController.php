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

        $roleName = $body['role'] ?? 'Reader';
        $roleId = $this->user->getRoleIdByName($roleName);

        if (!$roleId) {
            $response->json(['error' => 'Invalid role specified or default role not found.'], 400);
        }

        $existingUser = $this->user->findByEmail($body['email']);
        if ($existingUser) {
            $response->json(['error' => 'Email already exists'], 400);
        }


        $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);

        if ($this->user->create($body['email'], $body['fullname'], $passwordHash, $roleId, $body['password'])) {
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

            // Set HttpOnly cookie for web routes (expires in 30 days)
            setcookie(
                'auth_token',
                $tokenStr,
                time() + (30 * 24 * 60 * 60),
                '/',
                '',
                false, // Set true if using HTTPS
                true   // HttpOnly
            );

            $response->json([
                'success' => true,
                'token' => $tokenStr,
                'role' => $user['role_name'] ?? 'Guest',
                'message' => 'Login successful'
            ]);
        }

        $response->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request, Response $response)
    {
        $bearerToken = $request->getBearerToken() ?? $_COOKIE['auth_token'] ?? null;
        if ($bearerToken) {
            $this->token->deleteByToken($bearerToken);
        }
        
        // Clear cookie
        setcookie('auth_token', '', time() - 3600, '/');

        $response->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
