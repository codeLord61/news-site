<?php

namespace app\core;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function renderView($view, $params = [])
    {
        return (new View())->renderView($view, $params);
    }

    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
