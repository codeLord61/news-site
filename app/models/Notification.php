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
        throw new \RuntimeException('TODO: implement Notification::create');
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
        throw new \RuntimeException('TODO: implement Notification::bulkCreate');
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
        throw new \RuntimeException('TODO: implement Notification::getForUser');
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
        throw new \RuntimeException('TODO: implement Notification::countUnreadForUser');
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
        throw new \RuntimeException('TODO: implement Notification::markOneRead');
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
        throw new \RuntimeException('TODO: implement Notification::markAllRead');
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
        throw new \RuntimeException('TODO: implement Notification::getEditorUserIds');
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
        throw new \RuntimeException('TODO: implement Notification::getAdminUserIds');
    }
}

