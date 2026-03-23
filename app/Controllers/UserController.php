<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\WebAuthMiddleware;
use app\models\User;
use app\models\Token;

class UserController extends Controller
{
    private User $user;
    private Token $token;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        
        // Protect all actions in this controller
        $this->registerMiddleware(new WebAuthMiddleware());
    }

    private function ensureAdmin(Request $request)
    {
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid($tokenStr);

        if (!$tokenData) {
            header("Location: " . url('/auth'));
            exit;
        }

        $userInfo = $this->user->findById($tokenData['user_id']);
        if (!$userInfo || strtolower($userInfo['role_name']) !== 'admin') {
            header("Location: " . url('/dashboard'));
            exit;
        }

        return $userInfo;
    }

    public function index(Request $request, Response $response)
    {
        $userInfo = $this->ensureAdmin($request);

        $userName = $userInfo['name'];
        $userEmail = $userInfo['email'];
        // Generate initials
        $words = explode(' ', $userName);
        $initials = '';
        foreach ($words as $w) {
            $initials .= strtoupper($w[0]);
            if (strlen($initials) >= 2) break;
        }
        if (empty($initials)) {
            $initials = 'U';
        }
        
        $roleName = $userInfo['role_name'] ?? 'User';

        $allUsers = $this->user->getAllUsers();

        $this->setLayout('dashboard');
        
        echo $this->render('dashboard/users', [
            'pageTitle'    => 'Manage Users',
            'pageSubtitle' => "View and manage all registered users.",
            'userName'     => $userName,
            'userInitials' => $initials,
            'userEmail'    => $userEmail,
            'userRole'     => strtolower($roleName),
            'users'        => $allUsers
        ]);
    }

    public function changeRole(Request $request, Response $response)
    {
        $this->ensureAdmin($request);

        if ($request->getMethod() !== 'post') {
            $response->setStatusCode(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        $data = $request->getBody();
        $userId = $data['user_id'] ?? null;
        $roleName = $data['role'] ?? null;

        if (!$userId || !$roleName) {
            $response->setStatusCode(400);
            echo json_encode(['error' => 'Missing user ID or role']);
            return;
        }

        $roleId = $this->user->getRoleIdByName(ucfirst($roleName));
        if (!$roleId) {
            $response->setStatusCode(400);
            echo json_encode(['error' => 'Invalid role']);
            return;
        }

        $success = $this->user->updateRole($userId, $roleId);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Role changed successfully']);
        } else {
            $response->setStatusCode(500);
            echo json_encode(['error' => 'Failed to update user role']);
        }
    }
}
