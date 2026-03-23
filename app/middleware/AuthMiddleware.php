<?php

namespace app\middleware;

use app\core\Middleware;
use app\core\Request;
use app\core\Response;
use app\models\Token;

class AuthMiddleware extends Middleware
{
    public function __construct(array $actions = [])
    {
        // If specific actions are passed, middleware only applies to them
        $this->actions = $actions;
    }

    public function execute(Request $request, Response $response, string $action)
    {
        if (empty($this->actions) || in_array($action, $this->actions)) {
            $token = $request->getBearerToken() ?? $_COOKIE['auth_token'] ?? null;
            
            if (!$token) {
                $response->json(['error' => 'Unauthorized: No token provided.'], 401);
                exit; // Should already be called in $response->json() but being safe.
            }

            $tokenModel = new Token();
            $tokenData = $tokenModel->findValid($token);

            if (!$tokenData) {
                $response->json(['error' => 'Unauthorized: Invalid or expired token'], 401);
                exit;
            }
        }
    }
}
