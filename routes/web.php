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

use app\controllers\DashboardController;
$this->router->get('/dashboard', [DashboardController::class, 'index']);
$this->router->get('/my-articles', [DashboardController::class, 'index']);
$this->router->get('/my-articles/{id}/preview', [DashboardController::class, 'previewMyArticle']);
$this->router->get('/submissions', [DashboardController::class, 'index']);
$this->router->get('/editor/articles', [DashboardController::class, 'index']);
$this->router->get('/editor/pending-submissions', [DashboardController::class, 'index']);
$this->router->get('/editor/pending-submissions/{id}/review', [DashboardController::class, 'reviewSubmission']);
$this->router->get('/editor/approved-articles', [DashboardController::class, 'index']);
$this->router->get('/editor/approved-articles/{id}/review', [DashboardController::class, 'reviewApprovedSubmission']);
$this->router->get('/editor/published-articles', [DashboardController::class, 'index']);
$this->router->get('/articles/new', [DashboardController::class, 'index']);
$this->router->get('/analytics', [DashboardController::class, 'index']);
$this->router->get('/admin/settings', [DashboardController::class, 'index']);
$this->router->post('/editor/articles/select', [DashboardController::class, 'selectSubmission']);
$this->router->post('/editor/pending-submissions/approve', [DashboardController::class, 'approveSubmission']);
$this->router->post('/editor/pending-submissions/reject', [DashboardController::class, 'rejectSubmission']);
$this->router->post('/editor/approved-articles/publish', [DashboardController::class, 'publishApprovedSubmission']);
$this->router->post('/editor/approved-articles/reject', [DashboardController::class, 'rejectApprovedSubmission']);

use app\controllers\UserController;
$this->router->get('/admin/users', [UserController::class, 'index']);
$this->router->post('/admin/users/change-role', [UserController::class, 'changeRole']);

use app\controllers\AccountController;
$this->router->get('/my-account', [AccountController::class, 'index']);
$this->router->post('/my-account/update', [AccountController::class, 'updateProfile']);

use app\controllers\CommentController;
$this->router->post('/comments/store', [CommentController::class, 'store']);

use app\controllers\BookmarkController;
$this->router->post('/bookmarks/toggle', [BookmarkController::class, 'toggle']);
