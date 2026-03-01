<?php
namespace app\core;

use app\controllers\HomeController;

class App
{
    public static string $basePath;
    public static Router $router;

    public function __construct($basePath)
    {
        self::$basePath = $basePath;
    }

    public static function run()
    {
        self::$router = new Router();
        // self::$basePath = dirname(__DIR__);
        // routes
        self::loadRoutes();

        // Resolve
        echo self::$router->resolve();
    }

    private static function loadRoutes()
    {

        self::$router->get('/', [HomeController::class, 'index']);
        self::$router->get('/about', [HomeController::class, 'about']);
    }

    public static function view($view, $data = [])
    {
        $controller = new Controller();

        extract($data);
        ob_start();
        require __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../views/layouts/main.php";
    }
    public static function assetPath($path)
    {
        $scriptName        = $_SERVER['SCRIPT_NAME'];
        $lastSlashPosition = strrpos($scriptName, '/');

        if ($lastSlashPosition !== false) {
            $scriptNameSliced = substr($scriptName, 0, $lastSlashPosition);
        }
        
        $final= $scriptNameSliced . "/assets/". $path;
        return $final;
    }
}