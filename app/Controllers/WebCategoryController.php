<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\services\CategoryService;

class WebCategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');
        $params = $request->getQueryParams();
        $page = max(1, (int)($params['page'] ?? 1));

        $data = $this->categoryService->getCategoryPageData($slug, $page);

        if (!$data) {
            // Render 404
            $response->setStatusCode(404);
            return $this->render('404', ['title' => 'Category Not Found']);
        }

        return $this->render('category', [
            'title' => $data['category']['name'] . ' - The Daily News',
            'category' => $data['category'],
            'featuredArticles' => $data['featuredArticles'],
            'latestArticles' => $data['latestArticles'],
            'pagination' => $data['pagination']
        ]);
    }
}
