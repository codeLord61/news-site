<?php

namespace app\models;

use app\core\Model;

class Tag extends Model
{
    /**
     * Get all tags with their published article counts.
     */
    public function getAllWithArticleCount(): array
    {
        $sql = "SELECT t.id, t.name, t.slug,
                       (SELECT COUNT(DISTINCT at2.article_id)
                        FROM articles_tags at2
                        INNER JOIN articles a ON at2.article_id = a.id
                        WHERE at2.tag_id = t.id
                          AND a.status = 'published'
                          AND a.deleted_at IS NULL
                       ) AS article_count
                FROM tags t
                ORDER BY t.name ASC";

        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find a single tag by slug.
     */
    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db()->prepare("SELECT id, name, slug FROM tags WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Count published articles with a tag.
     */
    public function countArticles(int $tagId): int
    {
        $sql = "SELECT COUNT(DISTINCT a.id)
                FROM articles a
                INNER JOIN articles_tags at2 ON a.id = at2.article_id
                WHERE at2.tag_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$tagId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get paginated published articles for a tag.
     */
    public function getArticles(int $tagId, int $limit, int $offset): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name
                FROM articles a
                INNER JOIN articles_tags at2 ON a.id = at2.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE at2.tag_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $tagId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return $articles;
    }
}
