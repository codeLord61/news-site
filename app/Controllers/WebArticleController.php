<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Article;

class WebArticleController extends Controller
{
    private Article $article;

    public function __construct()
    {
        $this->article = new Article();
    }

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

        return $this->render('article', [
            'title' => $article['title'] . ' - The Daily News',
            'article' => $article,
            'primaryCategory' => $primaryCategory,
            'primaryCategorySlug' => $primaryCategorySlug
        ]);
    }
}
