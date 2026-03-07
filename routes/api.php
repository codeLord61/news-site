<?php

/** @var \app\core\App $this */

use app\controllers\AuthController;

$this->router->post('/api/v1/login', [AuthController::class , 'login']);
$this->router->post('/api/v1/register', [AuthController::class , 'register']);
$this->router->post('/api/v1/logout', [AuthController::class , 'logout']);
