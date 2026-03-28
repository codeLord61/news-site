<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\User;
use app\models\Token;
use app\models\Bookmark;
use app\models\Article;

class BookmarkController extends Controller
{
    private User $user;
    private Token $token;
    private Bookmark $bookmark;
    private Article $article;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->bookmark = new Bookmark();
        $this->article = new Article();
    }

    /**
     * Input: Request $request (JSON payload containing article_id), Response $response
     * Output: JSON response with success and new bookmarked state.
     * Description: Toggles the bookmark status.
     */
    public function toggle(Request $request, Response $response)
    {
        // Require POST method
        if ($request->getMethod() !== 'post') {
            $response->setStatusCode(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        // Resolve logged in user from cookie/token
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid((string)$tokenStr);
        // Current state: $tokenData holds valid session or false
        
        if (!$tokenData) {
            $response->setStatusCode(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = (int)$tokenData['user_id'];
        // Current state: $userId extracted from token
        
        // Read JSON payload
        $body = json_decode(file_get_contents('php://input'), true);
        $articleId = isset($body['article_id']) ? (int)$body['article_id'] : 0;
        // Current state: $articleId parsed from the JSON body
        
        if (!$articleId) {
            $response->setStatusCode(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing article ID']);
            exit;
        }
        
        // Toggle the bookmark
        $isBookmarked = $this->bookmark->toggleBookmark($userId, $articleId);
        // Current state: $isBookmarked holds true if newly bookmarked, false if removed
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'bookmarked' => $isBookmarked]);
        // Current state: JSON response returned to the client
        exit;
    }
}
