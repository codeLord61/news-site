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
        // Inside AuthMiddleware, if the token is invalid or missing, it currently returns a 401 JSON.
        // For web routes, we want it to redirect. We will create a WebAuthMiddleware shortly to handle the redirect.
        
        // Get the token from cookie or header
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        
        if (!$tokenStr) {
            // Fallback (though middleware should catch this first)
            header("Location: " . url('/auth'));
            exit;
        }

        // Validate token and fetch user ID
        $tokenData = $this->token->findValid($tokenStr);
        if (!$tokenData) {
            header("Location: " . url('/auth'));
            exit;
        }

        // Fetch user info including role
        $userInfo = $this->user->findById($tokenData['user_id']);
        
        if (!$userInfo) {
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
