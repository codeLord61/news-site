<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Tag;

class TagController extends Controller
{
    private Tag $tag;

    /**
     * Initialize tag model for tag API endpoints.
     */
    public function __construct()
    {
        $this->tag = new Tag();
    }

    /**
     * GET /api/v1/tags
     * List all tags with article counts.
     */
    public function index(Request $request, Response $response)
    {
        $tags = $this->tag->getAllWithArticleCount();
        $response->json(['data' => $tags]);
    }

    /**
     * GET /api/v1/tags/{slug}
     * Get articles by a specific tag (paginated).
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (!$slug) {
            $response->json(['error' => 'Tag slug is required'], 400);
        }

        $tag = $this->tag->findBySlug($slug);

        if (!$tag) {
            $response->json(['error' => 'Tag not found'], 404);
        }

        // Pagination
        $params = $request->getQueryParams();
        // Convert query strings into numeric pagination values.
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(50, max(1, (int)($params['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;

        $total = $this->tag->countArticles($tag['id']);
        $articles = $this->tag->getArticles($tag['id'], $limit, $offset);

        $response->json([
            'data' => [
                'tag' => $tag,
                'articles' => $articles,
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => (int)ceil($total / $limit),
            ],
        ]);
    }
}
