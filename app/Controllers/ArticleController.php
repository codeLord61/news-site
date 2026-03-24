<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Article;

class ArticleController extends Controller
{
    private Article $article;

    /**
     * Prepare article model dependency for API endpoints.
     */
    public function __construct()
    {
        $this->article = new Article();
    }

    /**
     * GET /api/v1/articles
     * List published articles with pagination, filtering, and search.
     */
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        // Convert raw query text into safe integers for pagination math.
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(50, max(1, (int)($params['limit'] ?? 15)));
        // Example: page=3, limit=15 -> offset=30
        $offset = ($page - 1) * $limit;
        $sort = $params['sort'] ?? 'latest';

        $filters = [
            'category' => $params['category'] ?? null,
            'tag' => $params['tag'] ?? null,
            'search' => $params['search'] ?? null,
        ];

        $result = $this->article->getPublished($filters, $sort, $limit, $offset);

        $response->json([
            'data' => $result['articles'],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $result['total'],
                'last_page' => (int)ceil($result['total'] / $limit),
            ],
        ]);
    }

    /**
     * GET /api/v1/articles/trending
     * Top articles by view_count within a recent period.
     */
    public function trending(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $limit = min(50, max(1, (int)($params['limit'] ?? 10)));
        $period = $params['period'] ?? 'week';

        switch ($period) {
            case 'day':
                $interval = '1 DAY';
                break;
            case 'month':
                $interval = '30 DAY';
                break;
            default:
                $interval = '7 DAY';
        }
        // Example mapping: "month" -> SQL interval "30 DAY".

        $articles = $this->article->getTrending($interval, $limit);

        $response->json([
            'data' => $articles,
            'period' => $period,
            'limit' => $limit,
        ]);
    }

    /**
     * GET /api/v1/articles/{slug}
     * Get a single article by slug. Increments view_count.
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (!$slug) {
            $response->json(['error' => 'Article slug is required'], 400);
        }

        $article = $this->article->findBySlug($slug);

        if (!$article) {
            $response->json(['error' => 'Article not found'], 404);
        }

        // Increment view count
        $this->article->incrementViewCount($article['id']);
        // Keep API response consistent with increment just performed in DB.
        $article['view_count']++;

        $response->json(['data' => $article]);
    }
}
