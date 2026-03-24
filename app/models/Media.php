<?php

namespace app\models;

use app\core\Model;

class Media extends Model
{
    /**
     * Get the thumbnail media linked to an article (is_thumbnail = 1).
     *
     * Input: article id.
     * @return array|null  ['id', 'file_url', 'alt_text', 'caption'] or null
     */
    public function getFirstForArticle(int $articleId): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT m.id, m.file_url, m.alt_text, m.caption
             FROM medias m
             INNER JOIN articles_medias am ON m.id = am.media_id
             WHERE am.article_id = ? AND m.is_thumbnail = 1
             LIMIT 1"
        );
        $stmt->execute([$articleId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Create an image media row.
     *
     * Input:
     * - $fileUrl: stored/remote image URL
     * - $altText: optional alt text
     * - $title: optional caption/title text
     * - $uploadedBy: reporter user id
     * @param bool $isThumbnail  True when this image is the article thumbnail.
     * Output: created media id.
     */
    public function createImage(
        string $fileUrl,
        ?string $altText,
        ?string $title,
        int $uploadedBy,
        bool $isThumbnail = false
    ): int {
        $stmt = $this->db()->prepare(
            "INSERT INTO medias (file_url, media_type, alt_text, caption, uploaded_by, is_thumbnail, created_at)
             VALUES (?, 'image', ?, ?, ?, ?, NOW())"
        );

        $stmt->execute([
            $fileUrl,
            $altText,
            $title,
            $uploadedBy,
            $isThumbnail ? 1 : 0,
        ]);

        return (int)$this->db()->lastInsertId();
    }

    /**
     * Update the is_thumbnail flag for a specific media row.
     *
     * Input: media id + bool flag.
     * Output: none (updates DB row).
     */
    public function setIsThumbnail(int $mediaId, bool $flag): void
    {
        $this->db()->prepare(
            "UPDATE medias SET is_thumbnail = ? WHERE id = ?"
        )->execute([$flag ? 1 : 0, $mediaId]);
    }

    /**
     * Returns only media IDs owned by the reporter.
     *
     * Input: candidate media ids + reporter id.
     * @return int[]
     */
    public function findOwnedIds(array $mediaIds, int $reporterId): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        // Normalize to unique positive integers before SQL IN query.
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
     *
     * Input: media id.
     * Output: media row array or false.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT id, file_url, media_type, caption, alt_text, uploaded_by, is_thumbnail, created_at
             FROM medias
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
