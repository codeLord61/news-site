<?php

namespace app\core;

use Dotenv\Dotenv;
use Exception;

class App
{
    public static App $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;

    public static string $ROOT_DIR;

    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        // 1. Load Environment Variables (.env)
        if (file_exists(dirname(__DIR__, 2) . '/.env')) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
        }

        // 2. Initialize Core Components
        $this->db = new Database();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);

        // 3. Register Routes (We will build this file soon)
        if (file_exists(dirname(__DIR__, 2) . '/routes/web.php')) {
            require_once dirname(__DIR__, 2) . '/routes/web.php';
        }
        if (file_exists(dirname(__DIR__, 2) . '/routes/api.php')) {
            require_once dirname(__DIR__, 2) . '/routes/api.php';
        }
    }

    /**
     * The heart of the application. 
     * It resolves the route and sends the output to the browser.
     */
    public function run(): void
    {
        try {
            // Tell the router to look at the current URI and find a match
            echo $this->router->resolve();
        }
        catch (Exception $e) {
            // Global Error Handling
            $this->response->setStatusCode((int)$e->getCode() ?: 500);

            // Output a basic error message if _error view is missing
            if (file_exists(self::$ROOT_DIR . '/app/Views/_error.php')) {
                echo(new View())->renderView('_error', ['exception' => $e]);
            }
            else {
                echo "<h1>An error occurred</h1>";
                echo "<p>" . $e->getMessage() . "</p>";
            }
        }
    }

    public static function assetPath($path)
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $lastSlashPosition = strrpos($scriptName, '/');

        if ($lastSlashPosition !== false) {
            $scriptNameSliced = substr($scriptName, 0, $lastSlashPosition);
        }
        else {
            $scriptNameSliced = '';
        }

        $final = $scriptNameSliced . "/assets/" . $path;
        return $final;
    }
}
