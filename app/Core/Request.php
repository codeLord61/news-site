<?php

namespace app\core;

class Request
{
    /**
     * Route path variables extracted by Router.
     * Example for /articles/{slug}: ['slug' => 'my-title'].
     */
    protected array $routeParams = [];

    /**
     * Store route params for current request cycle.
     *
     * Input: associative array of route parameters.
     * Output: none (updates $this->routeParams).
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Read one route parameter by name.
     *
     * Input: param name + optional fallback default.
     * Output: param value if present, else $default.
     */
    public function getRouteParam(string $name, $default = null)
    {
        return $this->routeParams[$name] ?? $default;
    }

    /**
     * Get all route parameters for this request.
     *
     * Output: associative array like ['id' => '12'].
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Read and sanitize query string values ($_GET).
     *
     * Input source: URL query parameters.
     * Output: sanitized associative array of query params.
     */
    public function getQueryParams(): array
    {
        $params = [];
        foreach ($_GET as $key => $value) {
            // Convert raw query values into safe strings.
            $params[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $params;
    }

    /**
     * Get request path without query string.
     *
     * Input source: $_SERVER['REQUEST_URI'].
     * Output: normalized path string (example: "/dashboard").
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptUrl = $_SERVER['SCRIPT_NAME'];

        // Handle paths when app is nested in a directory like /news-site/public/
        if (strpos($path, dirname($scriptUrl)) === 0) {
            $path = substr($path, strlen(dirname($scriptUrl)));
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }

        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    /**
     * Get HTTP method in lowercase.
     *
     * Output examples: "get", "post".
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');
    }

    /**
     * Parse request body based on content type and method.
     *
     * Input source:
     * - JSON payload from php://input when Content-Type is application/json
     * - $_GET for GET requests
     * - $_POST for POST requests
     * Output: associative array payload.
     */
    public function getBody()
    {
        $body = [];

        // Check if the request is JSON
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            // Transform JSON text into PHP associative array.
            $data = json_decode($json, true);
            if (is_array($data)) {
                $body = $data;
            }
            return $body;
        }

        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    /**
     * Read one HTTP header value (case-insensitive).
     *
     * Input: header name, for example "Authorization".
     * Output: string header value or null.
     */
    public function getHeader($name)
    {
        // For compatibility with nginx or other web servers
        // $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $headers = apache_request_headers();
        if (!$headers) {
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$headerName] = $value;
                }
            }
        }
        $name = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $name) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Extract bearer token from Authorization header.
     *
     * Input header example: "Authorization: Bearer abc123".
     * Output: "abc123" or null when missing/invalid format.
     */
    public function getBearerToken()
    {
        $header = $this->getHeader('Authorization');
        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
