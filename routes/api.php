<?php

use app\controllers\AuthController;
use app\controllers\ArticleController;
use app\controllers\CategoryController;
use app\controllers\ReporterArticleController;
use app\controllers\TagController;
use app\controllers\NotificationController;

/** @var \app\core\App $this */

// ---- Auth routes ----
$this->router->post('/api/v1/login', [AuthController::class , 'login']);
$this->router->post('/api/v1/register', [AuthController::class , 'register']);
$this->router->post('/api/v1/logout', [AuthController::class , 'logout']);
$this->router->get('/api/v1/auth/me', [AuthController::class , 'me']);

// ---- Public Article routes ----
$this->router->get('/api/v1/articles', [ArticleController::class , 'index']);
$this->router->get('/api/v1/articles/trending', [ArticleController::class , 'trending']);
$this->router->get('/api/v1/articles/{slug}', [ArticleController::class , 'show']);

// ---- Reporter Article routes ----
$this->router->post('/api/v1/reporter/articles', [ReporterArticleController::class, 'save']);
$this->router->post('/api/v1/reporter/articles/delete', [ReporterArticleController::class, 'delete']);
$this->router->post('/api/v1/reporter/media/images', [ReporterArticleController::class, 'uploadImage']);

// ---- Public Category routes ----
$this->router->get('/api/v1/categories', [CategoryController::class , 'index']);
$this->router->get('/api/v1/categories/{slug}', [CategoryController::class , 'show']);

// ---- Public Tag routes ----
$this->router->get('/api/v1/tags', [TagController::class , 'index']);
$this->router->get('/api/v1/tags/{slug}', [TagController::class , 'show']);

// ---- Notification routes (TODO wire after implementing controller methods) ----
$this->router->get('/api/v1/notifications', [NotificationController::class, 'index']);
$this->router->post('/api/v1/notifications/read', [NotificationController::class, 'markOneRead']);
$this->router->post('/api/v1/notifications/read-all', [NotificationController::class, 'markAllRead']);