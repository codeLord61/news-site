<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\User;
use app\models\Token;
use app\models\Comment;

class CommentController extends Controller
{
    private User $user;
    private Token $token;
    private Comment $comment;

    /**
     * Initialize models needed to create comments as logged-in users.
     */
    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->comment = new Comment();
    }

    /**
     * Store a new comment for an article.
     *
     * Input (POST form):
     * - article_id (int)
     * - content (string)
     * - article_slug (string, used for redirect)
     * Output: redirect response to article page or home.
     */
    public function store(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'post') {
            header("Location: " . url('/'));
            exit;
        }

        $userInfo = $this->resolveWebUser($request);
        
        $articleId = filter_var($_POST['article_id'] ?? 0, FILTER_VALIDATE_INT);
        $content = trim($_POST['content'] ?? '');
        $articleSlug = $_POST['article_slug'] ?? '';

        if ($articleId > 0 && !empty($content)) {
            // Persist comment row tied to article + current user.
            $this->comment->create($articleId, (int)$userInfo['id'], $content);
        }

        if (!empty($articleSlug)) {
            header("Location: " . url('/articles/' . $articleSlug));
            exit;
        }

        header("Location: " . url('/'));
        exit;
    }

    /**
     * Resolve authenticated web user from token.
     *
     * Output: user row array.
     * Side effects: clears invalid cookie and redirects to /auth when unauthorized.
     */
    private function resolveWebUser(Request $request): array
    {
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid((string)$tokenStr);

        if (!$tokenData) {
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        $userInfo = $this->user->findById((int)$tokenData['user_id']);
        if (!$userInfo) {
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        return $userInfo;
    }
}
