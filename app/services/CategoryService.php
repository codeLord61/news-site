<?php

namespace app\services;

use app\models\Article;
use app\models\Category;

class CategoryService
{
    private Article $article;
    private Category $category;

    public function __construct()
    {
        $this->article = new Article();
        $this->category = new Category();
    }

    /**
     * Get data for the dynamic category page.
     *
     * @return array|false Returns false if category not found.
     */
    public function getCategoryPageData(string $slug, int $page = 1)
    {
        $categoryDetails = $this->category->findBySlug($slug);
        if (!$categoryDetails) {
            return false;
        }

        $categoryId = (int)$categoryDetails['id'];
        
        // Count total articles for pagination calculation
        $totalArticles = $this->category->countArticles($categoryId);

        // Featured: First page, first 5 articles
        $featuredLimit = 5;
        $featuredArticles = [];
        
        // Latest News (others)
        $latestLimit = 9;
        
        if ($page === 1) {
            // First page: get 5 featured + 9 latest = 14 total
            $allArticles = $this->article->getPaginatedByCategory($categoryId, $featuredLimit + $latestLimit, 0);
            
            $featuredArticles = array_slice($allArticles, 0, $featuredLimit);
            $latestArticles = array_slice($allArticles, $featuredLimit);
        } else {
            // Subsequent pages: Only show "Latest News", offset by the 5 featured from page 1
            $offset = $featuredLimit + (($page - 1) * $latestLimit);
            $latestArticles = $this->article->getPaginatedByCategory($categoryId, $latestLimit, $offset);
        }

        $totalPages = ceil(($totalArticles - $featuredLimit) / $latestLimit) + 1;
        if ($totalPages <= 0) {
            $totalPages = 1;
        }

        return [
            'category' => $categoryDetails,
            'featuredArticles' => $featuredArticles,
            'latestArticles' => $latestArticles,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1,
            ]
        ];
    }
}
