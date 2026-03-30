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
        throw new \RuntimeException('TODO: implement NotificationService::notifyEditorsArticleSubmitted');
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
        throw new \RuntimeException('TODO: implement NotificationService::notifyReporterArticleSelected');
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
     *
     * Link:
     * - "/my-articles"
     */
    public function notifyReporterReviewResult(
        int $reporterUserId,
        string $articleTitle,
        string $editorName,
        string $status
    ): int {
        throw new \RuntimeException('TODO: implement NotificationService::notifyReporterReviewResult');
    }

    /**
     * TODO: Notify reporter when editor publishes article.
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
     * - "Your article \"{title}\" was published by {editorName}."
     *
     * Link:
     * - "/my-articles"
     */
    public function notifyReporterPublished(int $reporterUserId, string $articleTitle, string $editorName): int
    {
        throw new \RuntimeException('TODO: implement NotificationService::notifyReporterPublished');
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
    public function notifyAdminsNewSignup(string $newUserName, string $newUserEmail): int
    {
        throw new \RuntimeException('TODO: implement NotificationService::notifyAdminsNewSignup');
    }
}

