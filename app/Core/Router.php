<?php

namespace app\core;

class Router
{
    protected array $routes = [];
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            $this->response->setStatusCode(404);
            return "404 Not Found";
        }

        if (is_string($callback)) {
            return (new View())->renderView($callback);
        }

        if (is_array($callback)) {
            // Instantiate the controller
            $controllerName = $callback[0];
            /** @var Controller $controller */
            $controller = new $controllerName();
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute($this->request, $this->response, $controller->action);
            }
        }

        return call_user_func($callback, $this->request, $this->response);
    }
}
