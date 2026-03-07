<?php

namespace app\core;

abstract class Middleware
{
    public array $actions = [];

    /**
     * @param Request $request
     * @param Response $response
     * @param string $action Current controller action being executed
     */
    abstract public function execute(Request $request, Response $response, string $action);
}
