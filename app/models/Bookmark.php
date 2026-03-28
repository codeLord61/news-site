<?php

namespace app\models;

use app\core\Model;
use PDO;

class Bookmark extends Model
{
    /**
     * Input: int $userId (ID of the user), int $articleId (ID of the article)
     * Output: bool (true if bookmark was added, false if it was removed)
     * Description: Toggles the bookmark status of an article for a given user.
     */
    public function toggleBookmark(int $userId, int $articleId): bool
    {
        // Check if bookmark currently exists
        $isBookmarked = $this->isBookmarked($userId, $articleId);
        // Current state: $isBookmarked boolean variable holds either true or false based on db check

        if ($isBookmarked) {
            // Bookmark exists, so we remove it
            $stmt = $this->db()->prepare("DELETE FROM users_bookmark_articles WHERE user_id = ? AND article_id = ?");
            $stmt->execute([$userId, $articleId]);
            // Current state: Bookmark deleted from users_bookmark_articles table. Returning false indicating it is no longer bookmarked.
            return false;
        } else {
            // Bookmark does not exist, so we add it
            $stmt = $this->db()->prepare("INSERT INTO users_bookmark_articles (user_id, article_id) VALUES (?, ?)");
            $stmt->execute([$userId, $articleId]);
            // Current state: Bookmark inserted into users_bookmark_articles table. Returning true indicating it is now bookmarked.
            return true;
        }
    }

    /**
     * Input: int $userId (ID of the user), int $articleId (ID of the article)
     * Output: bool (true if user has bookmarked the article, false otherwise)
     */
    public function isBookmarked(int $userId, int $articleId): bool
    {
        $stmt = $this->db()->prepare("SELECT 1 FROM users_bookmark_articles WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$userId, $articleId]);
        // Current state: Query executed, checking if fetchColumn() returns anything truthy
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Input: int $userId (ID of the user for whom to fetch bookmarks)
     * Output: array (List of bookmarked articles with category and thumbnail info)
     */
    public function getUserBookmarks(int $userId): array
    {
        // Use MAX or GROUP BY to aggregate duplicates, similar to comments setup
        $stmt = $this->db()->prepare("
            SELECT b.created_at as bookmarked_at, 
                   a.id as article_id, a.title as article_title, a.slug as article_slug, 
                   a.excerpt as article_excerpt, a.published_at,
                   MAX(m.file_url) as article_thumbnail
            FROM users_bookmark_articles b
            JOIN articles a ON b.article_id = a.id
            LEFT JOIN articles_medias am ON a.id = am.article_id
            LEFT JOIN medias m ON am.media_id = m.id AND m.is_thumbnail = 1
            WHERE b.user_id = ?
            GROUP BY b.user_id, b.article_id, a.id, b.created_at
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$userId]);
        // Current state: $bookmarks array contains raw SQL result rows
        $bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Current state: returning multi-dimensional array of bookmarks
        return $bookmarks;
    }

    /**
     * Input: int $userId, int $articleId
     * Output: void
     * Description: Removes a specific bookmark.
     */
    public function removeBookmark(int $userId, int $articleId): void
    {
        $stmt = $this->db()->prepare("DELETE FROM users_bookmark_articles WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$userId, $articleId]);
        // Current state: Bookmark deleted for user
    }
}
