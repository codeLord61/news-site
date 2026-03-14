<?php

namespace app\services;

use app\models\Article;
use app\models\Category;
use app\config\HomepageConfig;

/**
 * Service layer that orchestrates data fetching for the homepage.
 * Keeps the controller thin by encapsulating the business logic here.
 */
class HomepageService
{
    private Article $article;
    private Category $category;

    public function __construct()
    {
        $this->article = new Article();
        $this->category = new Category();
    }

    /**
     * Get the latest articles for the hero section.
     */
    public function getHeroArticles(): array
    {
        return $this->article->getLatest(HomepageConfig::HERO_ARTICLE_COUNT);
    }

    /**
     * Get all category sections with their articles.
     * The category matching SECOND_CATEGORY_SLUG appears first,
     * followed by all other categories (alphabetical).
     *
     * @return array  Each element: ['category' => [...], 'articles' => [...]]
     */
    public function getCategorySections(): array
    {
        $allCategories = $this->category->getAllWithArticleCount();

        $secondCategory = null;
        $otherCategories = [];

        foreach ($allCategories as $cat) {
            // Skip categories with no published articles
            if ((int)($cat['article_count'] ?? 0) === 0) {
                continue;
            }

            if ($cat['slug'] === HomepageConfig::SECOND_CATEGORY_SLUG) {
                $secondCategory = $cat;
            } else {
                $otherCategories[] = $cat;
            }
        }

        $sections = [];
        $limit = HomepageConfig::CATEGORY_ARTICLE_COUNT;

        // Second section: the priority category (Bangladesh)
        if ($secondCategory) {
            $sections[] = [
                'category' => $secondCategory,
                'articles' => $this->article->getPublishedByCategory((int)$secondCategory['id'], $limit),
            ];
        }

        // Remaining category sections
        foreach ($otherCategories as $cat) {
            $articles = $this->article->getPublishedByCategory((int)$cat['id'], $limit);
            if (!empty($articles)) {
                $sections[] = [
                    'category' => $cat,
                    'articles' => $articles,
                ];
            }
        }

        return $sections;
    }

    /**
     * Get all homepage data in a single call.
     *
     * @return array ['hero' => [...], 'sections' => [...]]
     */
    public function getHomePageData(): array
    {
        return [
            'hero' => $this->getHeroArticles(),
            'sections' => $this->getCategorySections(),
        ];
    }
}
