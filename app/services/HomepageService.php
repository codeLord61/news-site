<?php

namespace app\services;

use app\config\HomepageConfig;
use app\controllers\HomeController;
use app\models\Article;
use app\models\Category;

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
     * Get the latest articles for the hero section
     * return : [{id: , title:,  slug: , reporter: {name:, username: }, published_at:, }, {...}, ... {...}]
     *      
     */
    public function getHeroArticles(): array
    {
        return $this->article->getLatest(HomepageConfig::HERO_ARTICLE_COUNT);
    }

    /**
     * Get all category sections with their articles.
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

        // Second section
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
     * ['hero' => ['category' : {}, 'articles' : {}], 'sections': [[...], [...], ..., [...]]
     * 
     * @return array{hero: array, sections: array}
     */
    public function getHomePageData()
    {
        return [
            'hero' => $this->getHeroArticles(),
            'sections' => $this->getCategorySections(),
        ];
    }
}