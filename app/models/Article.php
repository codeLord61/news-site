<?php
namespace app\models;

use app\core\Model;

class Article extends Model
{
    /**
     * Get paginated published articles with optional category, tag, and search filters.
     *
     * @return array{articles: array, total: int}
     */
    public function getPublished(array $filters, string $sort, int $limit, int $offset): array
    {
        $category = $filters['category'] ?? null;
        $tag      = $filters['tag'] ?? null;
        $search   = $filters['search'] ?? null;

        $baseSelect = "SELECT DISTINCT a.id, a.title, a.slug, a.excerpt, a.status,
                        a.published_at, a.view_count, a.share_count,
                        u.name AS reporter_name, u.username AS reporter_username";
        $baseFrom = " FROM articles a
                       LEFT JOIN users u ON a.reporter_id = u.id";
        $conditions = " WHERE a.status = 'published' AND a.deleted_at IS NULL";
        $bindParams = [];

        if ($category) {
            $baseFrom .= " INNER JOIN articles_categories ac ON a.id = ac.article_id
                           INNER JOIN categories c ON ac.category_id = c.id";
            $conditions   .= " AND c.slug = ?";
            $bindParams[]  = $category;
        }

        if ($tag) {
            $baseFrom .= " INNER JOIN articles_tags at2 ON a.id = at2.article_id
                           INNER JOIN tags t ON at2.tag_id = t.id";
            $conditions   .= " AND t.slug = ?";
            $bindParams[]  = $tag;
        }

        if ($search) {
            $conditions   .= " AND MATCH(a.title, a.content) AGAINST(? IN NATURAL LANGUAGE MODE)";
            $bindParams[]  = $search;
        }

        switch ($sort) {
            case 'popular':
                $orderBy = " ORDER BY a.view_count DESC";
                break;
            case 'oldest':
                $orderBy = " ORDER BY a.published_at ASC";
                break;
            default:
                $orderBy = " ORDER BY a.published_at DESC";
        }

        // Count total
        $countSql  = "SELECT COUNT(DISTINCT a.id)" . $baseFrom . $conditions;
        $countStmt = $this->db()->prepare($countSql);
        $countStmt->execute($bindParams);
        $total = (int) $countStmt->fetchColumn();

        // Fetch paginated rows
        $sql  = $baseSelect . $baseFrom . $conditions . $orderBy . " LIMIT ? OFFSET ?";
        $stmt = $this->db()->prepare($sql);
        $i    = 1;
        foreach ($bindParams as $val) {
            $stmt->bindValue($i++, $val);
        }
        $stmt->bindValue($i++, $limit, \PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Enrich with categories, tags, and nested reporter
        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['tags']       = $this->getTagsForArticle($row['id']);
            $row['reporter']   = [
                'name'     => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        return ['articles' => $articles, 'total' => $total];
    }

    /**
     * Get trending articles within a time interval.
     */
    public function getTrending(string $interval, int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.status = 'published'
                  AND a.deleted_at IS NULL
                  AND a.published_at >= NOW() - INTERVAL $interval
                ORDER BY a.view_count DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['tags']       = $this->getTagsForArticle($row['id']);
            $row['reporter']   = [
                'name'     => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Find a single published article by slug.
     */
    public function findBySlug(string $slug): array | false
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.status,
                       a.published_at, a.view_count, a.share_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.slug = ? AND a.status = 'published' AND a.deleted_at IS NULL";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$slug]);
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (! $article) {
            return false;
        }

        $article['categories'] = $this->getCategoriesForArticle($article['id']);
        $article['tags']       = $this->getTagsForArticle($article['id']);
        $article['reporter']   = [
            'name'     => $article['reporter_name'],
            'username' => $article['reporter_username'],
        ];
        unset($article['reporter_name'], $article['reporter_username']);

        return $article;
    }

    /**
     * Increment the view count for an article.
     */
    public function incrementViewCount(int $id): void
    {
        $this->db()->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")
            ->execute([$id]);
    }

    /**
     * Get the latest N published articles (regardless of category).
     * Used for the hero section on the homepage.
     */
    public function getLatest(int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id
                        ORDER BY m.id ASC LIMIT 1) AS alt_text,
                        
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id
                        ORDER BY m.id ASC LIMIT 1) AS thumbnail
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.status = 'published' AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            // Without unsetting, reporter_name, reporter_username key, value would also show
            // So there will be unnecessary duplication, as we grouped name & username under reporter already
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Get published articles for a specific category.
     * Used for category sections on the homepage.
     */
    public function getPublishedByCategory(int $categoryId, int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id
                        ORDER BY m.id ASC LIMIT 1) AS alt_text,
                        
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id
                        ORDER BY m.id ASC LIMIT 1) AS thumbnail
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Get categories associated with an article.
     */
    public function getCategoriesForArticle(int $articleId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN articles_categories ac ON c.id = ac.category_id
             WHERE ac.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get tags associated with an article.
     */
    public function getTagsForArticle(int $articleId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT t.id, t.name, t.slug
             FROM tags t
             INNER JOIN articles_tags at2 ON t.id = at2.tag_id
             WHERE at2.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}