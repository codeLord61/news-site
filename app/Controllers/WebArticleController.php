<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Article;
use app\models\Token;
use app\models\User;
use app\models\Comment;
use app\models\Bookmark;

class WebArticleController extends Controller
{
    private Article $article;
    private Token $token;
    private User $user;
    private Comment $comment;
    private Bookmark $bookmark;

    /**
     * Initialize models needed to render article detail pages.
     */
    public function __construct()
    {
        $this->article = new Article();
        $this->token = new Token();
        $this->user = new User();
        $this->comment = new Comment();
        $this->bookmark = new Bookmark();
    }

    /**
     * Render one article page by slug.
     *
     * Input: route param {slug}.
     * Output: HTML article page or 404 page.
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');
        if (!$slug) {
            $response->setStatusCode(400);
            return $this->render('404', ['title' => 'Bad Request']);
        }

        $article = $this->article->findBySlug($slug);

        if (!$article) {
            $response->setStatusCode(404);
            return $this->render('404', ['title' => 'Article Not Found']);
        }

        // Increment view count asynchronously (conceptually - in PHP it runs synchronously before render)
        // Optimization: It's often better to decouple this via queues, but for this app this is fine.
        $this->article->incrementViewCount($article['id']);

        // Determine main category to show in header/breadcrumbs
        $primaryCategory = !empty($article['categories']) ? $article['categories'][0]['name'] : 'News';
        $primaryCategorySlug = !empty($article['categories']) ? $article['categories'][0]['slug'] : '';

        $currentUser = null;
        $isBookmarked = false;
        
        if (isset($_COOKIE['auth_token'])) {
            // If cookie is valid, include current user so view can show auth-aware UI.
            $tokenData = $this->token->findValid($_COOKIE['auth_token']);
            if ($tokenData) {
                $currentUser = $this->user->findById((int)$tokenData['user_id']);
                // Current state: $currentUser contains user details if logged in
                if ($currentUser) {
                    $isBookmarked = $this->bookmark->isBookmarked((int)$currentUser['id'], (int)$article['id']);
                    // Current state: $isBookmarked is boolean true if user bookmarked this article
                }
            }
        }

        $comments = $this->comment->getByArticleId($article['id']);

        return $this->render('article', [
            'title' => $article['title'] . ' - Packly News',
            'article' => $article,
            'primaryCategory' => $primaryCategory,
            'primaryCategorySlug' => $primaryCategorySlug,
            'currentUser' => $currentUser,
            'comments' => $comments,
            'isBookmarked' => $isBookmarked
        ]);
    }
}
