<?php

namespace app\models;

use app\core\Model;

class Category extends Model
{
    /**
     * Get all categories with their published article counts.
     */
    public function getAllWithArticleCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, c.description, c.parent_id,
                       (SELECT COUNT(DISTINCT ac.article_id)
                        FROM articles_categories ac
                        INNER JOIN articles a ON ac.article_id = a.id
                        WHERE ac.category_id = c.id
                          AND a.status = 'published'
                          AND a.deleted_at IS NULL
                       ) AS article_count
                FROM categories c
                ORDER BY c.name ASC";

        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find a single category by slug.
     */
    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT id, name, slug, description, parent_id FROM categories WHERE slug = ?"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Count published articles in a category.
     */
    public function countArticles(int $categoryId): int
    {
        $sql = "SELECT COUNT(DISTINCT a.id)
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$categoryId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get paginated published articles for a category.
     */
    public function getArticles(int $categoryId, int $limit, int $offset): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
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
     * Get child categories for a parent category.
     */
    public function getChildren(int $categoryId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT id, name, slug FROM categories WHERE parent_id = ?"
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}