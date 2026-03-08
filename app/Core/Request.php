<?php

namespace app\core;

class Request
{
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

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');
    }

    public function getBody()
    {
        $body = [];

        // Check if the request is JSON
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
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

    public function getBearerToken()
    {
        $header = $this->getHeader('Authorization');
        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}