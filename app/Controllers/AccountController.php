<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\User;
use app\models\Token;
use app\models\Comment;

class AccountController extends Controller
{
    private User $user;
    private Token $token;
    private Comment $comment;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->comment = new Comment();
    }

    public function index(Request $request, Response $response)
    {
        $userInfo = $this->resolveWebUser($request);

        // Fetch user's comments
        $comments = $this->comment->getByUserId((int)$userInfo['id']);

        $this->setLayout('main');

        echo $this->render('account', [
            'pageTitle' => 'My Account - Packly News',
            'user' => $userInfo,
            'comments' => $comments
        ]);
    }

    public function updateProfile(Request $request, Response $response)
    {
        if ($request->getMethod() !== 'post') {
            header("Location: " . url('/my-account'));
            exit;
        }

        $userInfo = $this->resolveWebUser($request);
        $userId = (int)$userInfo['id'];

        $name = trim($_POST['name'] ?? '');
        
        // Handle avatar upload if present
        $avatarPath = $userInfo['avatar_path'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $avatarPath = '/uploads/avatars/' . $fileName;
            }
        }

        if (!empty($name)) {
            // Update user in DB
            $this->user->updateProfile($userId, $name, $avatarPath);
        }

        header("Location: " . url('/my-account'));
        exit;
    }

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
