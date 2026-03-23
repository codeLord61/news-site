<?php

namespace app\models;

use app\core\Model;

class Media extends Model
{
    /**
     * Get the first media (thumbnail) linked to an article.
     *
     * @return array|null  ['file_url', 'alt_text', 'caption'] or null
     */
    public function getFirstForArticle(int $articleId): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT m.id, m.file_url, m.alt_text, m.caption
             FROM medias m
             INNER JOIN articles_medias am ON m.id = am.media_id
             WHERE am.article_id = ?
             ORDER BY m.id ASC
             LIMIT 1"
        );
        $stmt->execute([$articleId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Create an image media row.
     */
    public function createImage(
        string $fileUrl,
        ?string $altText,
        ?string $title,
        int $uploadedBy
    ): int {
        $stmt = $this->db()->prepare(
            "INSERT INTO medias (file_url, media_type, alt_text, caption, uploaded_by, created_at)
             VALUES (?, 'image', ?, ?, ?, NOW())"
        );

        $stmt->execute([
            $fileUrl,
            $altText,
            $title,
            $uploadedBy,
        ]);

        return (int)$this->db()->lastInsertId();
    }

    /**
     * Returns only media IDs owned by the reporter.
     *
     * @return int[]
     */
    public function findOwnedIds(array $mediaIds, int $reporterId): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        $mediaIds = array_values(array_unique(array_map('intval', $mediaIds)));
        $placeholders = implode(',', array_fill(0, count($mediaIds), '?'));

        $sql = "SELECT id
                FROM medias
                WHERE uploaded_by = ?
                  AND media_type = 'image'
                  AND id IN ($placeholders)";

        $stmt = $this->db()->prepare($sql);
        $params = array_merge([$reporterId], $mediaIds);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(static fn(array $row): int => (int)$row['id'], $rows);
    }

    /**
     * Fetch a media row by id.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT id, file_url, media_type, caption, alt_text, uploaded_by, created_at
             FROM medias
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
