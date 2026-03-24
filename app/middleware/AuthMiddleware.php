<?php

namespace app\middleware;

use app\core\Middleware;
use app\core\Request;
use app\core\Response;
use app\models\Token;

class AuthMiddleware extends Middleware
{
    /**
     * Input: optional action list where this middleware should run.
     * Output: none (stores list in $this->actions).
     */
    public function __construct(array $actions = [])
    {
        // If specific actions are passed, middleware only applies to them
        $this->actions = $actions;
    }

    /**
     * Validate API auth token before protected actions.
     *
     * Input:
     * - bearer token from Authorization header OR auth_token cookie
     * - current action name
     * Output:
     * - continues request when token is valid
     * - sends 401 JSON and exits when invalid
     */
    public function execute(Request $request, Response $response, string $action)
    {
        if (empty($this->actions) || in_array($action, $this->actions)) {
            // Prefer Authorization header, fallback to cookie for browser clients.
            $token = $request->getBearerToken() ?? $_COOKIE['auth_token'] ?? null;
            
            if (!$token) {
                $response->json(['error' => 'Unauthorized: No token provided.'], 401);
                exit; // Should already be called in $response->json() but being safe.
            }

            $tokenModel = new Token();
            $tokenData = $tokenModel->findValid($token);

            if (!$tokenData) {
                // Invalid token means user context cannot be trusted.
                $response->json(['error' => 'Unauthorized: Invalid or expired token'], 401);
                exit;
            }
        }
    }
}
