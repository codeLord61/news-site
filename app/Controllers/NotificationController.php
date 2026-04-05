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
    
    /**
     * GET /api/v1/notifications
     * Return notification list + unread count for the current user.
     */
    public function index(Request $request, Response $response): void
    {   
        $user = $this->resolveAuthUser($request, $response);

        $limit = (int)($request->getQueryParams()['limit'] ?? 10);
        if($limit < 1 || $limit > 50) {
            $limit = 10;
        }

        $unreadOnly = filter_var(
            $request->getQueryParams()['unread_only'] ?? false, 
            FILTER_VALIDATE_BOOLEAN);
        
        $items = $this->notification->getForUser($user['id'], $limit, $unreadOnly);
        $unreadCount = $this->notification->countUnreadForUser((int)$user['id']);
        
        $response->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'unread_count' => $unreadCount
            ]
        ]);
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
    /**
     * POST /api/v1/notifications/read
     * Mark one specific notification as read.
     */
    public function markOneRead(Request $request, Response $response): void
    {   
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }
        
        $user = $this->resolveAuthUser($request, $response);
        $body = $request->getBody();

        $notificationId = filter_var($body['notification_id'] ?? null, FILTER_VALIDATE_INT);
        
        if ($notificationId === false || $notificationId < 0) {
            $response->json(['error' => 'notification_id is required and must be a positive integer.'], 422);
        }

        $marked = $this->notification->markOneRead((int)$notificationId, (int)$user['id']);

        if (!$marked) {
            $response->json(['error' => 'Notification not found or already read'], 404);
        } 

        $response->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ]);
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
    /**
     * POST /api/v1/notifications/read-all
     * Mark all notifications as read for the current user.
     */
    public function markAllRead(Request $request, Response $response): void
    {   
        if ($request->getMethod() !== 'post'){
            $response->json(['error' => 'Method not allowed'], 405);
        }

        $user = $this->resolveAuthUser($request, $response);

        $affectedCount = $this->notification->markAllRead((int)$user['id']);
        
        $response->json([
            'success' => true,
            'affected' => $affectedCount,
            'message' => $affectedCount > 0 ? 'All notifications marked as read.' : 'No unread notifications found.'
        ]);
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
    /**
     * Resolve authenticated user from token.
     */
    private function resolveAuthUser(Request $request, Response $response): array
    {   
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        if (!$tokenStr) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        $tokenData = $this->token->findValid((string)$tokenStr);
        if (!$tokenData) {
            $response->json(['error' => 'Unauthorized'], 401);
        }
        
        $userInfo = $this->user->findById((int)$tokenData['user_id']);
        if (!$userInfo) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        return $userInfo;
    }
}