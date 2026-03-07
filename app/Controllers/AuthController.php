<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\services\TokenService;
use app\middleware\AuthMiddleware;

class AuthController extends Controller
{
    public function __construct()
    {
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

        $db = App::$app->db;

        $stmt = $db->pdo->prepare("SELECT id FROM roles WHERE name = 'Reader'");
        $stmt->execute();
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            $response->json(['error' => 'Default role not found. Ensure migrations/seeders have run.'], 500);
        }

        $stmt = $db->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$body['email']]);
        if ($stmt->fetch()) {
            $response->json(['error' => 'Email already exists'], 400);
        }

        $username = explode('@', $body['email'])[0] . random_int(1000, 9999);
        $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);

        $stmt = $db->pdo->prepare("INSERT INTO users (username, email, name, password, role_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $body['email'], $body['fullname'], $passwordHash, $roleId])) {
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

        $db = App::$app->db;
        $stmt = $db->pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$body['email']]);
        $user = $stmt->fetch();

        if ($user && password_verify($body['password'], $user['password'])) {
            $token = TokenService::generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $db->pdo->prepare("INSERT INTO personal_access_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiresAt]);

            $response->json([
                'success' => true,
                'token' => $token,
                'message' => 'Login successful'
            ]);
        }

        $response->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request, Response $response)
    {
        $token = $request->getBearerToken();
        if ($token) {
            $db = App::$app->db;
            $stmt = $db->pdo->prepare("DELETE FROM personal_access_tokens WHERE token = ?");
            $stmt->execute([$token]);
        }
        $response->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
