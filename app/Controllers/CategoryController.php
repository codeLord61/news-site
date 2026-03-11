<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Category;

class CategoryController extends Controller
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    /**
     * GET /api/v1/categories
     * List all categories with parent_id for hierarchy building.
     */
    public function index(Request $request, Response $response)
    {
        $categories = $this->category->getAllWithArticleCount();
        $response->json(['data' => $categories]);
    }

    /**
     * GET /api/v1/categories/{slug}
     * Get a single category with its paginated articles.
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (!$slug) {
            $response->json(['error' => 'Category slug is required'], 400);
        }

        $category = $this->category->findBySlug($slug);

        if (!$category) {
            $response->json(['error' => 'Category not found'], 404);
        }

        // Pagination
        $params = $request->getQueryParams();
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(50, max(1, (int)($params['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;

        $total = $this->category->countArticles($category['id']);
        $articles = $this->category->getArticles($category['id'], $limit, $offset);
        $category['children'] = $this->category->getChildren($category['id']);

        $response->json([
            'data' => [
                'category' => $category,
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
