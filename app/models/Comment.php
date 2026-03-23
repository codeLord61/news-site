<?php

namespace app\models;

use app\core\Model;
use PDO;

class Comment extends Model
{
    /**
     * Create a new comment.
     */
    public function create(int $articleId, int $userId, string $content): bool
    {
        // By default, let's auto-approve for logged in users as per standard behavior
        $stmt = $this->db()->prepare("
            INSERT INTO comments (article_id, user_id, content, is_approved)
            VALUES (?, ?, ?, 1)
        ");
        return $stmt->execute([$articleId, $userId, $content]);
    }

    /**
     * Get approved comments for an article by its ID.
     */
    public function getByArticleId(int $articleId): array
    {
        $stmt = $this->db()->prepare("
            SELECT c.*, u.name as user_name, u.avatar_path as user_avatar
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.article_id = ? AND c.is_approved = 1
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all comments for a user.
     */
    public function getByUserId(int $userId): array
    {
        // Use MAX to aggregate potential duplicates and explicitly add a.id to GROUP BY
        $stmt = $this->db()->prepare("
            SELECT c.*, a.title as article_title, a.slug as article_slug,
                   MAX(m.file_url) as article_thumbnail
            FROM comments c
            JOIN articles a ON c.article_id = a.id
            LEFT JOIN articles_medias am ON a.id = am.article_id
            LEFT JOIN medias m ON am.media_id = m.id AND m.is_thumbnail = 1
            WHERE c.user_id = ?
            GROUP BY c.id, a.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
