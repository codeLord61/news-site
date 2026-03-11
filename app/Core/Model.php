<?php

namespace app\core;

abstract class Model
{
    /**
     * Get the PDO instance from the shared Database connection.
     */
    protected function db(): \PDO
    {
        return App::$app->db->pdo;
    }
}
