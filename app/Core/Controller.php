<?php

namespace app\core;

abstract class Controller
{
    public string $layout = 'main';
    public string $action = '';
    /** @var Middleware[] */
    protected array $middlewares = [];

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        $viewObj = new View();
        $viewObj->layout = $this->layout;
        return $viewObj->renderView($view, $params);
    }

    public function registerMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
