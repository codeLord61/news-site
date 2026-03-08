<?php

namespace app\core;

abstract class Middleware
{
    public array $actions = [];

    abstract public function execute(Request $request, Response $response, string $action);
}