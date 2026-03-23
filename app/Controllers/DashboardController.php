<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\WebAuthMiddleware;
use app\models\Category;
use app\models\Tag;
use app\models\User;
use app\models\Token;

class DashboardController extends Controller
{
    private User $user;
    private Token $token;
    private Category $category;
    private Tag $tag;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->category = new Category();
        $this->tag = new Tag();
        
        // Protect all actions in this controller
        $this->registerMiddleware(new WebAuthMiddleware());
    }

    public function index(Request $request, Response $response)
    {
        // Get the token from cookie or header (Middleware already validated its existence/validity)
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid((string)$tokenStr);

        // Fetch user info including role
        $userInfo = $this->user->findById((int)$tokenData['user_id']);
        
        if (!$userInfo) {
            // This case is rare if token is valid, but good to have
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        // Extract required variables for the dashboard layout & partials
        $userName = $userInfo['name'];
        $userEmail = $userInfo['email'];
        $initials = $this->buildInitials($userName);
        
        $roleName = $userInfo['role_name'] ?? 'User';
        $normalizedRole = strtolower((string)$roleName);
        $currentPath = $request->getPath();
        
        // Define if this user should see the dashboard
        $allowedRoles = ['Admin', 'Editor', 'Reporter'];
        if (!in_array($roleName, $allowedRoles, true)) {
            // Readers / Guests shouldn't access CMS
            header("Location: " . url('/'));
            exit;
        }

        $this->setLayout('dashboard');

        $baseViewData = [
            'userName'     => $userName,
            'userInitials' => $initials,
            'userEmail'    => $userEmail,
            'userRole'     => $normalizedRole,
            'currentPath'  => $currentPath,
        ];

        if ($currentPath === '/articles/new') {
            if ($normalizedRole !== 'reporter') {
                header("Location: " . url('/dashboard'));
                exit;
            }

            echo $this->render('dashboard/new_article', array_merge($baseViewData, [
                'pageTitle'    => 'New Article',
                'pageSubtitle' => 'Create a new story and save it as draft or submit for review.',
                'categories'   => $this->category->getAllWithArticleCount(),
                'tags'         => $this->tag->getAllWithArticleCount(),
            ]));
            return;
        }

        echo $this->render('dashboard/index', array_merge($baseViewData, [
            'pageTitle'    => 'Dashboard',
            'pageSubtitle' => "Welcome back, $userName! Here's what's happening.",
        ]));
    }

    private function buildInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }
            $initials .= strtoupper($word[0]);
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials !== '' ? $initials : 'U';
    }
}
