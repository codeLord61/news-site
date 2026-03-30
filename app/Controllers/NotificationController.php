<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\AuthMiddleware;
use app\models\Notification;
use app\models\Token;
use app\models\User;

class NotificationController extends Controller
{
    private Notification $notification;
    private Token $token;
    private User $user;

    /**
     * Initialize dependencies and protect API endpoints.
     *
     * Input: none.
     * Output: constructed controller instance.
     */
    public function __construct()
    {
        $this->notification = new Notification();
        $this->token = new Token();
        $this->user = new User();

        $this->registerMiddleware(new AuthMiddleware());
    }

    /**
     * TODO: Return notification list for the authenticated user.
     *
     * Input:
     * - Request with auth token (cookie or bearer).
     * - Optional query params:
     *   - limit: int (default 10)
     *   - unread_only: bool-like string ("1" / "true" / "0" / "false")
     *
     * Output (JSON):
     * - 200:
     *   {
     *     "success": true,
     *     "data": {
     *       "items": array<int, array{
     *         id: int,
     *         message: string,
     *         link: string,
     *         is_read: int,
     *         created_at: string
     *       }>,
     *       "unread_count": int
     *     }
     *   }
     * - 401 when unauthorized.
     */
    public function index(Request $request, Response $response): void
    {
        $response->json([
            'error' => 'TODO: implement NotificationController::index',
        ], 501);
    }

    /**
     * TODO: Mark one notification as read for current user.
     *
     * Input:
     * - Request body: { "notification_id": int }
     * - Auth token identifies current user.
     *
     * Output (JSON):
     * - 200: { "success": true, "message": "Notification marked as read." }
     * - 404 when notification does not belong to current user.
     * - 422 when notification_id is invalid.
     */
    public function markOneRead(Request $request, Response $response): void
    {
        $response->json([
            'error' => 'TODO: implement NotificationController::markOneRead',
        ], 501);
    }

    /**
     * TODO: Mark all notifications as read for current user.
     *
     * Input:
     * - Auth token identifies current user.
     *
     * Output (JSON):
     * - 200: { "success": true, "affected": int }
     */
    public function markAllRead(Request $request, Response $response): void
    {
        $response->json([
            'error' => 'TODO: implement NotificationController::markAllRead',
        ], 501);
    }

    /**
     * TODO: Resolve authenticated user from token.
     *
     * Input:
     * - Request with auth token from cookie or bearer header.
     *
     * Output:
     * - array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     role_name?: string
     *   }
     * - On failure: send 401 JSON and terminate request flow.
     */
    private function resolveAuthUser(Request $request, Response $response): array
    {
        $response->json([
            'error' => 'TODO: implement NotificationController::resolveAuthUser',
        ], 501);
    }
}

