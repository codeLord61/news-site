<?php

use app\controllers\HomeController;

/** @var \app\core\App $this */
$this->router->get('/', [HomeController::class , 'index']);
$this->router->get('/index.php', [HomeController::class , 'index']);

use app\controllers\WebCategoryController;
$this->router->get('/categories/{slug}', [WebCategoryController::class, 'show']);

use app\controllers\WebArticleController;
$this->router->get('/articles/{slug}', [WebArticleController::class, 'show']);

use app\controllers\AuthController;
$this->router->get('/auth', [AuthController::class , 'index']);
$this->router->get('/auth.html', [AuthController::class , 'index']);
