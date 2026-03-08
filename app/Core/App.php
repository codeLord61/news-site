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
    public static string $PROJECT_ROOT_URL;

    public function __construct($rootPath)
    {
        self::$ROOT_DIR         = $rootPath;
        self::$PROJECT_ROOT_URL = $this->getProjectRootURL();
        self::$app              = $this;

        // Load Environment Variables
        if (file_exists(dirname(__DIR__, 2) . '/.env')) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
        }

        date_default_timezone_set($_ENV['TIMEZONE']);

        // Initialize Core Components
        $this->db       = new Database();
        $this->request  = new Request();
        $this->response = new Response();
        $this->router   = new Router($this->request, $this->response);

        // Register Routes
        if (file_exists(dirname(__DIR__, 2) . '/routes/web.php')) {
            require_once dirname(__DIR__, 2) . '/routes/web.php';
        }
        if (file_exists(dirname(__DIR__, 2) . '/routes/api.php')) {
            require_once dirname(__DIR__, 2) . '/routes/api.php';
        }
    }

    public function run(): void
    {
        try {
            // Tell the router to look at the current URI and find a match
            echo $this->router->resolve();
        } catch (Exception $e) {
            // Global Error Handling
            $this->response->setStatusCode((int) $e->getCode() ?: 500);

            // Output a basic error message if _error view is missing
            if (file_exists(self::$ROOT_DIR . '/app/views/_error.php')) {
                echo(new View())->renderView('_error', ['exception' => $e]);
            } else {
                echo "<h1>An error occurred</h1>";
                echo "<p>" . $e->getMessage() . "</p>";
            }
        }
    }

    public static function assetPath($path): string
    {
        $scriptName        = $_SERVER['SCRIPT_NAME'];
        $lastSlashPosition = strrpos($scriptName, '/');

        if ($lastSlashPosition !== false) {
            $scriptNameSliced = substr($scriptName, 0, $lastSlashPosition);
        } else {
            $scriptNameSliced = '';
        }

        $final = $scriptNameSliced . "/assets/" . $path;
        return $final;
    }

    public function getProjectRootURL(): string
    {
        $script_name = $_SERVER['SCRIPT_NAME'];
        $entryPos    = strpos($script_name, '/index.php');
        $url         = substr($script_name, 0, $entryPos);
        return $url;
    }
}