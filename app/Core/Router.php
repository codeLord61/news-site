<?php

namespace app\core;

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
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

    // 1. Get the script name (e.g., /project/news/public/index.php)
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // 2. Derive the base directory by removing the filename
    // Result: /project/news/public
    $baseDir = str_replace('\\', '/', dirname($scriptName));

    // 3. Remove Query Strings (?id=123)
    $path = parse_url($requestUri, PHP_URL_PATH);

    // 4. Universal Cleanup: Remove the base directory from the start of the path
    if ($baseDir !== '/' && strpos($path, $baseDir) === 0) {
        $path = substr($path, strlen($baseDir));
    }

    // 5. Final Sanitization
    // Remove "index.php" if it was explicitly typed in the URL
    $path = str_replace('/index.php', '', $path);
    
    // Ensure it starts with / and isn't empty
    $path = '/' . ltrim($path, '/');

    // DEBUG (Remove these once working)
    // var_dump("Final Route Path: " . $path); 

    $callback = $this->routes[$method][$path] ?? false;

    if ($callback === false) {
        http_response_code(404);
        echo "404 Not Found";
        exit;
    }

    // Handle string callbacks (views)
    if (is_string($callback)) {
        return App::view($callback);
    }

    // Handle array callbacks ([Controller, Method])
    if (is_array($callback)) {
        $controller = new $callback[0]();
        $callback[0] = $controller;
    }

    return call_user_func($callback);
    }
}