<?php

namespace app\core;

abstract class Controller
{
    /**
     * Layout name used by View when rendering HTML pages.
     *
     * Expected value example: "main", "auth", "dashboard".
     */
    public string $layout = 'main';

    /**
     * Current action name resolved by router (for middleware filtering).
     */
    public string $action = '';

    /** @var Middleware[] */
    protected array $middlewares = [];

    /**
     * Set which layout wrapper should be used for upcoming render calls.
     *
     * Input: layout string (for example "dashboard").
     * Output: none (updates $this->layout in controller state).
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Render a view file with optional data parameters.
     *
     * Input: view name (string) + params array.
     * Output: rendered HTML string from View::renderView().
     */
    public function render($view, $params = [])
    {
        $viewObj = new View();
        // Pass current controller layout into the view renderer.
        $viewObj->layout = $this->layout;
        return $viewObj->renderView($view, $params);
    }

    /**
     * Attach middleware instance to this controller.
     *
     * Input: Middleware object.
     * Output: none (pushes into $this->middlewares array).
     */
    public function registerMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Return all middleware attached to this controller.
     *
     * Input: none.
     * Output: array of Middleware objects.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
