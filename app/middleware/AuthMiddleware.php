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
            $token = $request->getBearerToken();
            if (!$token) {
                $response->json(['error' => 'Unauthorized: No token provided. Please include Authorization: Bearer <token>'], 401);
            }

            $tokenModel = new Token();
            $tokenData = $tokenModel->findValid($token);

            if (!$tokenData) {
                $response->json(['error' => 'Unauthorized: Invalid or expired token'], 401);
            }

        // Further logic could attach user object to App if needed
        }
    }
}
