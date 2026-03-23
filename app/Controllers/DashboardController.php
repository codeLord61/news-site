<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\WebAuthMiddleware;
use app\models\User;
use app\models\Token;

class DashboardController extends Controller
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

    public function index(Request $request, Response $response)
    {
        // Get the token from cookie or header (Middleware already validated its existence/validity)
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid($tokenStr);

        // Fetch user info including role
        $userInfo = $this->user->findById($tokenData['user_id']);
        
        if (!$userInfo) {
            // This case is rare if token is valid, but good to have
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        // Extract required variables for the dashboard layout & partials
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
        
        // Define if this user should see the dashboard
        $allowedRoles = ['Admin', 'Editor', 'Reporter'];
        if (!in_array($roleName, $allowedRoles)) {
            // Readers / Guests shouldn't access CMS
            header("Location: " . url('/'));
            exit;
        }

        $this->setLayout('dashboard');
        
        echo $this->render('dashboard/index', [
            // Standard layout variables
            'pageTitle'    => 'Dashboard',
            'pageSubtitle' => "Welcome back, $userName! Here's what's happening.",
            'userName'     => $userName,
            'userInitials' => $initials,
            'userEmail'    => $userEmail,
            'userRole'     => strtolower($roleName)
        ]);
    }
}
