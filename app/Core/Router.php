<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query strings
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        // Adjust path based on your RewriteBase in .htaccess
        $basePath = '/news-site/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        if ($path === '') {
            $path = '/';
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            http_response_code(404);
            echo "404 Not Found";
            exit;
        }

        if (is_string($callback)) {
            // Can render view directly if callback is a string
            return App::view($callback);
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $callback[0] = $controller;
        }

        return call_user_func($callback);
    }
}
