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

        // 1) Try exact (static) match first
        $callback = $this->routes[$method][$path] ?? false;

        // 2) If no static match, try dynamic routes with {param} placeholders
        if ($callback === false && isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $cb) {
                // Only test routes that contain a placeholder
                if (strpos($route, '{') === false) {
                    continue;
                }

                // Convert /api/v1/articles/{slug} → regex
                $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $path, $matches)) {
                    // Store matched params on the request
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $this->request->setRouteParams($params);
                    $callback = $cb;
                    break;
                }
            }
        }

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
