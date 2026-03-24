<?php

namespace app\core;

abstract class Middleware
{
    /**
     * Optional list of action names this middleware should run for.
     * Empty array means run for all actions.
     *
     * Example: ['logout', 'index'].
     */
    public array $actions = [];

    /**
     * Execute middleware logic before controller action runs.
     *
     * Input:
     * - $request: current HTTP request wrapper.
     * - $response: response helper (json/status helpers).
     * - $action: controller method name about to run.
     * Output: middleware-defined (usually void). Implementations may stop request with redirect/json+exit.
     */
    abstract public function execute(Request $request, Response $response, string $action);
}
