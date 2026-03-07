<?php

use app\controllers\HomeController;

/** @var \app\core\App $this */
$this->router->get('/', [HomeController::class , 'index']);
$this->router->get('/index.php', [HomeController::class , 'index']);

use app\controllers\AuthController;
$this->router->get('/auth', [AuthController::class , 'index']);
$this->router->get('/auth.html', [AuthController::class , 'index']);
