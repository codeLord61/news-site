<?php

namespace app\services;

use app\models\Notification;

class NotificationService
{
    private Notification $notification;

    /**
     * Input: none.
     * Output: constructed service instance.
     */
    public function __construct()
    {
        $this->notification = new Notification();
    }

    /**
     * TODO: Notify all editors when reporter submits an article.
     *
     * Input:
     * - $articleId: int
     * - $articleTitle: string
     * - $reporterName: string
     *
     * Output:
     * - int inserted notification count.
     *
     * Message format:
     * - "New article \"{title}\" has been submitted by {reporterName}."
     *
     * Link:
     * - "/editor/articles"
     */
    public function notifyEditorsArticleSubmitted(int $articleId, string $articleTitle, string $reporterName): int
    {
        $message = "New article \"{$articleTitle}\" has been submitted by {$reporterName}.";
        $link = "/editor/articles";

        $editorIds = $this->notification->getEditorUserIds();
        return $this->notification->bulkCreate($editorIds, $message, $link);
    }

    /**
     * TODO: Notify reporter when editor selects article from queue.
     *
     * Input:
     * - $reporterUserId: int
     * - $articleTitle: string
     * - $editorName: string
     *
     * Output:
     * - int notification id.
     *
     * Message format:
     * - "Your article \"{title}\" was selected by {editorName}."
     *
     * Link:
     * - "/my-articles"
     */
    public function notifyReporterArticleSelected(int $reporterUserId, string $articleTitle, string $editorName): int
    {
        $message = "You article \"{$articleTitle}\" was selected by {$editorName}";
        $link = "/my-articles";

        return $this->notification->create($reporterUserId, $message, $link);
    }

    /**
     * TODO: Notify reporter when editor approves or rejects article.
     *
     * Input:
     * - $reporterUserId: int
     * - $articleTitle: string
     * - $editorName: string
     * - $status: string must be "approved" or "rejected"
     *
     * Output:
     * - int notification id.
     *
     * Message format:
     * - "Your article \"{title}\" was approved by {editorName}."
     * - "Your article \"{title}\" was rejected by {editorName}."
     * - "Your article \"{title}\" was published by {editorName}."
     * Link:
     * - "/my-articles"
     */
    public function notifyReporterReviewResult(
        int $reporterUserId,
        string $articleTitle,
        string $editorName,
        string $status
    ): int {
        $message = "Your article \"{$articleTitle}\" was {$status} by {$editorName}.";
        $link = "/my-articles";
        return $this->notification->create($reporterUserId, $message, $link);
    }

  
    /**
     * TODO: Notify all admins when a new user registers.
     *
     * Input:
     * - $newUserName: string
     * - $newUserEmail: string
     *
     * Output:
     * - int inserted notification count.
     *
     * Message format:
     * - "New user {name} ({email}) signed up."
     *
     * Link:
     * - "/admin/users"
     */
    public function notifyAdminsNewSignup(string $newUserName): int
    {
        $message = "New user \"{$newUserName}\" signed up.";
        $link = "/admin/users";
        
        $adminIds = $this->notification->getAdminUserIds();
        return $this->notification->bulkCreate($adminIds, $message, $link);
    }
}