<?php

namespace app\core;

class Response
{
    /**
     * Set outgoing HTTP status code.
     *
     * Input: integer code (200, 404, 422, etc.).
     * Output: none (affects PHP response state).
     */
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    /**
     * Render a view template into HTML.
     *
     * Input: view name + params array.
     * Output: rendered HTML string.
     */
    public function renderView($view, $params = [])
    {
        return (new View())->renderView($view, $params);
    }

    /**
     * Return JSON response and terminate script.
     *
     * Input:
     * - $data: any serializable array/object/scalar.
     * - $statusCode: HTTP status code.
     * Output: none (echoes JSON and exits).
     */
    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json');
        // Transform PHP data structure into JSON text for API clients.
        echo json_encode($data);
        exit;
    }
}
