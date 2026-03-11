<?php

use app\controllers\AuthController;
use app\controllers\ArticleController;
use app\controllers\CategoryController;

// Auth routes
$this->router->post('/api/v1/login', [AuthController::class , 'login']);
$this->router->post('/api/v1/register', [AuthController::class , 'register']);
$this->router->post('/api/v1/logout', [AuthController::class , 'logout']);

// Public routes
// Article 
$this->router->get('/api/v1/articles', [ArticleController::class, 'index']);
$this->router->get('/api/v1/articles/{slug}', [ArticleController::class, 'show']);