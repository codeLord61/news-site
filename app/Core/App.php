<?php

namespace App\Core;

class App
{
    public static Router $router;

    public static function run()
    {
        self::$router = new Router();

        // Define routes
        self::loadRoutes();

        // Resolve
        echo self::$router->resolve();
    }

    private static function loadRoutes()
    {
        // This is a simple central place to load routes. 
        // Can be moved to routes/web.php later.

        self::$router->get('/', [\App\Controllers\HomeController::class , 'index']);
    }

    public static function view($view, $data = [])
    {
        $controller = new Controller();
        // Expose protected view method. Alternatively handled via traits / direct requiring
        // A hacky quick way mapping string route to view
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../Views/layouts/main.php";
    }
}
