<?php

namespace app\middleware;

use app\core\Middleware;
use app\core\Request;
use app\core\Response;
use app\models\Token;

/**
 * WebAuthMiddleware handles authentication for Web (HTML) routes.
 * If unauthorized, it redirects to the login page instead of returning JSON.
 */
class WebAuthMiddleware extends Middleware
{
    /**
     * Input: optional list of action names that require web auth.
     * Output: none (stores action list).
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Validate auth for HTML routes.
     *
     * Input: request token (cookie/header) + action name.
     * Output:
     * - allows request when token is valid
     * - redirects to /auth and exits when unauthorized
     */
    public function execute(Request $request, Response $response, string $action)
    {
        if (empty($this->actions) || in_array($action, $this->actions)) {
            // For web, primarily check the cookie
            $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
            
            if (!$tokenStr) {
                // If no token, redirect to auth page
                header("Location: " . url('/auth'));
                exit;
            }

            $tokenModel = new Token();
            $tokenData = $tokenModel->findValid($tokenStr);

            if (!$tokenData) {
                // Invalid or expired token: clear the bad cookie and redirect
                setcookie('auth_token', '', time() - 3600, '/');
                header("Location: " . url('/auth'));
                exit;
            }
        }
    }
}
