<?php

namespace app\models;

use app\core\Model;

class Notification extends Model
{
    /**
     * TODO: Create one notification row.
     *
     * Input:
     * - $userId: int receiver user id.
     * - $message: string notification text.
     * - $link: string absolute app path (example: "/my-articles").
     *
     * Output:
     * - int newly inserted notification id.
     */
    public function create(int $userId, string $message, string $link): int
    {
        $sql = "INSERT INTO notifications (user_id, message, link, is_read, created_at) 
                VALUES (?, ?, ?, 0, NOW())";
                
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $message, \PDO::PARAM_STR);
        $stmt->bindValue(3, $link, \PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->db()->lastInsertId();
    }

    /**
     * TODO: Create the same notification for multiple users.
     *
     * Input:
     * - $userIds: int[] receiver ids.
     * - $message: string
     * - $link: string
     *
     * Output:
     * - int number of rows inserted.
     */
    public function bulkCreate(array $userIds, string $message, string $link): int
    {
        if (empty($userIds)) {
            return 0;
        }

        $stmt = $this->db()->prepare("INSERT INTO notifications (user_id, message, link, is_read, created_at)
                                    VALUES (?, ?, ?, 0, NOW());");
        
        $count = 0;
        foreach ($userIds as $userId) {
            $stmt->execute([(int) $userId, $message, $link]);
            $count++;
        }

        return $count;
    }

    /**
     * TODO: Get latest notifications for one user.
     *
     * Input:
     * - $userId: int
     * - $limit: int default 10
     * - $unreadOnly: bool default false
     *
     * Output:
     * - array<int, array{
     *     id: int,
     *     user_id: int,
     *     message: string,
     *     link: string,
     *     is_read: int,
     *     created_at: string
     *   }>
     */
    public function getForUser(int $userId, int $limit = 10, bool $unreadOnly = false): array
    {
        $sql = "SELECT id, message, link, created_at
                FROM notifications
                WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * TODO: Count unread notifications for one user.
     *
     * Input:
     * - $userId: int
     *
     * Output:
     * - int unread count.
     */
    public function countUnreadForUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications 
                WHERE user_id = ? AND is_read = 0";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * TODO: Mark one notification as read if owned by user.
     *
     * Input:
     * - $notificationId: int
     * - $userId: int owner check
     *
     * Output:
     * - bool true if one row changed, false otherwise.
     */
    public function markOneRead(int $notificationId, int $userId): bool
    {
        $sql = "UPDATE notifications
                SET is_read = 1 
                WHERE user_id = ? AND id = ? AND is_read = 0";
        
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $notificationId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * TODO: Mark all notifications as read for one user.
     *
     * Input:
     * - $userId: int
     *
     * Output:
     * - int affected row count.
     */
    public function markAllRead(int $userId): int
    {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * TODO: Get editor user IDs.
     * Useful when reporter submits an article.
     *
     * Input: none.
     * Output: int[] editor user ids.
     */
    public function getEditorUserIds(): array
    {
        $sql = "SELECT u.id 
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE r.name = 'Editor'
                ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        
        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'id');
    }

    /**
     * TODO: Get admin user IDs.
     * Useful when a new user registers.
     *
     * Input: none.
     * Output: int[] admin user ids.
     */
    public function getAdminUserIds(): array
    {
        $sql = "SELECT u.id 
                FROM users u
                JOIN roles r ON r.id = u.role_id
                WHERE r.name = 'Admin'
                ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        
        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'id');
    }
}