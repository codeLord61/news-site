<?php

use app\controllers\HomeController;

/** @var \app\core\App $this */
$this->router->get('/', [HomeController::class , 'index']);
$this->router->get('/index.php', [HomeController::class , 'index']);
