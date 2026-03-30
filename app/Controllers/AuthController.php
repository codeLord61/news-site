<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\services\TokenService;
use app\middleware\AuthMiddleware;
use app\models\User;
use app\models\Token;
use app\services\NotificationService;

class AuthController extends Controller
{
    private User $user;
    private Token $token;
    private NotificationService $notificationService;

    /**
     * Initialize auth-related models.
     */
    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->notificationService = new NotificationService();
        // Middleware was only applied to logout, but we want logout to always be reachable 
        // to ensure cookies are cleared even if the token is expired.
    }

    public function index()
    {
        // If already logged in, redirect to dashboard
        $tokenStr = $_COOKIE['auth_token'] ?? null;
        if ($tokenStr && $this->token->findValid($tokenStr)) {
            header("Location: " . url('/dashboard'));
            exit;
        }

        $this->setLayout('auth');
        echo $this->render('auth', [
            'title' => 'Packly News - Sign In'
        ]);
    }

    /**
     * Register a new reader account.
     *
     * Input body: fullname, email, password.
     * Output JSON: success/error message.
     */
    public function register(Request $request, Response $response)
    {
        $body = $request->getBody();
        if (empty($body['fullname']) || empty($body['email']) || empty($body['password'])) {
            $response->json(['error' => 'Missing required fields: fullname, email, password'], 400);
        }

        $roleName = 'Reader';
        // Convert role name to foreign key role_id for users table.
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
            /**
             * TODO(notification):
             * Input:
             * - fullname: string ($body['fullname'])
             * - email: string ($body['email'])
             * Output:
             * - int inserted notification count for admin users.
             *
             * How it should look:
             * - message: "New user {fullname} ({email}) signed up."
             * - link: "/admin/users"
             */
            // $this->notificationService->notifyAdminsNewSignup((string)$body['fullname'], (string)$body['email']);

            $response->json(['success' => true, 'message' => 'Registration successful! You may now log in.']);
        }

        $response->json(['error' => 'Registration failed due to server error'], 500);
    }

    /**
     * Authenticate user and issue token.
     *
     * Input body: email, password.
     * Output JSON: token + role + message, and sets auth_token cookie.
     */
    public function login(Request $request, Response $response)
    {
        $body = $request->getBody();
        if (empty($body['email']) || empty($body['password'])) {
            $response->json(['error' => 'Missing required fields: email, password'], 400);
        }

        $user = $this->user->findByEmail($body['email']);

        if ($user && password_verify($body['password'], $user['password'])) {
            // Random token string used for API auth and cookie-based web auth.
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

    /**
     * Logout current user by revoking token and clearing cookie.
     *
     * Input: bearer token or auth_token cookie.
     * Output JSON: logout result.
     */
    public function logout(Request $request, Response $response)
    {
        $bearerToken = $request->getBearerToken() ?? $_COOKIE['auth_token'] ?? null;
        if ($bearerToken) {
            $this->token->deleteByToken($bearerToken);
        }
        
        // Clear cookie with same flags as set in login()
        setcookie(
            'auth_token', 
            '', 
            time() - 3600, 
            '/', 
            '', 
            false, 
            true
        );

        $response->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    /**
     * Return authenticated user's profile summary.
     *
     * Input: bearer token or auth_token cookie.
     * Output JSON: id, name, email, role.
     */
    public function me(Request $request, Response $response)
    {
        $tokenStr = $request->getBearerToken() ?? $_COOKIE['auth_token'] ?? null;
        if (!$tokenStr) {
            $response->json(['error' => 'Not authenticated'], 401);
        }

        $tokenData = $this->token->findValid($tokenStr);
        if (!$tokenData) {
            $response->json(['error' => 'Invalid or expired token'], 401);
        }

        $userInfo = $this->user->findById($tokenData['user_id']);
        if (!$userInfo) {
            $response->json(['error' => 'User not found'], 404);
        }

        $response->json([
            'success' => true,
            'user' => [
                'id' => $userInfo['id'],
                'name' => $userInfo['name'],
                'email' => $userInfo['email'],
                'role' => $userInfo['role_name']
            ]
        ]);
    }
}
