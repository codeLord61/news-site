<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\middleware\WebAuthMiddleware;
use app\models\Article;
use app\models\Category;
use app\models\Tag;
use app\models\User;
use app\models\Token;

class DashboardController extends Controller
{
    private User $user;
    private Token $token;
    private Category $category;
    private Tag $tag;
    private Article $article;

    public function __construct()
    {
        $this->user = new User();
        $this->token = new Token();
        $this->category = new Category();
        $this->tag = new Tag();
        $this->article = new Article();
        
        // Protect all actions in this controller
        $this->registerMiddleware(new WebAuthMiddleware());
    }

    public function index(Request $request, Response $response)
    {
        $userInfo = $this->resolveWebUser($request);

        // Extract required variables for the dashboard layout & partials
        $userName = $userInfo['name'];
        $userEmail = $userInfo['email'];
        $initials = $this->buildInitials($userName);
        
        $roleName = $userInfo['role_name'] ?? 'User';
        $normalizedRole = strtolower((string)$roleName);
        $currentPath = $request->getPath();
        
        // Define if this user should see the dashboard
        $allowedRoles = ['Admin', 'Editor', 'Reporter'];
        if (!in_array($roleName, $allowedRoles, true)) {
            // Readers / Guests shouldn't access CMS
            header("Location: " . url('/'));
            exit;
        }

        $this->setLayout('dashboard');

        $baseViewData = [
            'userName'     => $userName,
            'userInitials' => $initials,
            'userEmail'    => $userEmail,
            'userRole'     => $normalizedRole,
            'currentPath'  => $currentPath,
        ];

        if ($currentPath === '/articles/new') {
            $this->ensureWebRole($normalizedRole, 'reporter');

            $queryParams = $request->getQueryParams();
            $articleId = filter_var($queryParams['article_id'] ?? null, FILTER_VALIDATE_INT);
            $initialArticle = null;

            if ($articleId !== false && (int)$articleId > 0) {
                $initialArticle = $this->article->getReporterArticleForForm((int)$articleId, (int)$userInfo['id']);
                if (!$initialArticle) {
                    header("Location: " . url('/my-articles'));
                    exit;
                }
            }

            echo $this->render('dashboard/new_article', array_merge($baseViewData, [
                'pageTitle'      => $initialArticle ? 'Edit Article' : 'New Article',
                'pageSubtitle'   => $initialArticle
                    ? 'Update your story and resubmit when ready.'
                    : 'Create a new story and save it as draft or submit for review.',
                'categories'     => $this->category->getAllWithArticleCount(),
                'tags'           => $this->tag->getAllWithArticleCount(),
                'initialArticle' => $initialArticle,
            ]));
            return;
        }

        if ($currentPath === '/my-articles') {
            $this->ensureWebRole($normalizedRole, 'reporter');

            echo $this->render('dashboard/reporter_my_articles', array_merge($baseViewData, [
                'pageTitle'    => 'My Articles',
                'pageSubtitle' => 'Track and manage all stories you created.',
                'articles'     => $this->article->getReporterArticles((int)$userInfo['id']),
            ]));
            return;
        }

        if ($currentPath === '/submissions') {
            $this->ensureWebRole($normalizedRole, 'reporter');

            echo $this->render('dashboard/reporter_my_articles', array_merge($baseViewData, [
                'pageTitle'     => 'Submissions',
                'pageSubtitle'  => 'Submitted and reviewed stories only.',
                'articles'      => $this->article->getReporterSubmissions((int)$userInfo['id']),
                'hideDraftHint' => true,
            ]));
            return;
        }

        if ($currentPath === '/editor/articles') {
            $this->ensureWebRole($normalizedRole, 'editor');

            echo $this->render('dashboard/editor_all_articles', array_merge($baseViewData, [
                'pageTitle'    => 'All Articles',
                'pageSubtitle' => 'Submitted stories waiting for an editor to pick them.',
                'articles'     => $this->article->getSubmittedForEditorQueue(),
            ]));
            return;
        }

        if ($currentPath === '/editor/pending-submissions') {
            $this->ensureWebRole($normalizedRole, 'editor');

            echo $this->render('dashboard/editor_pending_submissions', array_merge($baseViewData, [
                'pageTitle'    => 'Pending Submissions',
                'pageSubtitle' => 'Articles currently assigned to you for review.',
                'articles'     => $this->article->getPendingForEditor((int)$userInfo['id']),
            ]));
            return;
        }

        if ($currentPath === '/editor/approved-articles') {
            $this->ensureWebRole($normalizedRole, 'editor');

            echo $this->render('dashboard/editor_approved_articles', array_merge($baseViewData, [
                'pageTitle'    => 'Approved Articles',
                'pageSubtitle' => 'Approved articles you can publish or move back to rejected.',
                'articles'     => $this->article->getApprovedForEditor((int)$userInfo['id']),
            ]));
            return;
        }

        echo $this->render('dashboard/index', array_merge($baseViewData, [
            'pageTitle'    => 'Dashboard',
            'pageSubtitle' => "Welcome back, $userName! Here's what's happening.",
        ]));
    }

    public function previewMyArticle(Request $request, Response $response): void
    {
        $userInfo = $this->resolveWebUser($request);
        $normalizedRole = strtolower((string)($userInfo['role_name'] ?? ''));
        $this->ensureWebRole($normalizedRole, 'reporter');

        $articleId = (int)$request->getRouteParam('id', 0);
        if ($articleId <= 0) {
            header("Location: " . url('/my-articles'));
            exit;
        }

        $article = $this->article->getReporterArticlePreview($articleId, (int)$userInfo['id']);
        if (!$article) {
            header("Location: " . url('/my-articles'));
            exit;
        }

        $this->setLayout('dashboard');

        echo $this->render('dashboard/reporter_article_preview', [
            'pageTitle'     => 'Article Preview',
            'pageSubtitle'  => 'Preview this article inside your dashboard.',
            'userName'      => $userInfo['name'],
            'userInitials'  => $this->buildInitials($userInfo['name']),
            'userEmail'     => $userInfo['email'],
            'userRole'      => $normalizedRole,
            'currentPath'   => $request->getPath(),
            'article'       => $article,
        ]);
    }

    public function reviewSubmission(Request $request, Response $response): void
    {
        $userInfo = $this->resolveWebUser($request);
        $normalizedRole = strtolower((string)($userInfo['role_name'] ?? ''));
        $this->ensureWebRole($normalizedRole, 'editor');

        $articleId = (int)$request->getRouteParam('id', 0);
        if ($articleId <= 0) {
            header("Location: " . url('/editor/pending-submissions'));
            exit;
        }

        $article = $this->article->getPendingArticleForEditor($articleId, (int)$userInfo['id']);
        if (!$article) {
            header("Location: " . url('/editor/pending-submissions'));
            exit;
        }

        $this->setLayout('dashboard');

        echo $this->render('dashboard/editor_review_submission', [
            'pageTitle'     => 'Review Submission',
            'pageSubtitle'  => 'Review content and decide whether to approve or reject.',
            'userName'      => $userInfo['name'],
            'userInitials'  => $this->buildInitials($userInfo['name']),
            'userEmail'     => $userInfo['email'],
            'userRole'      => $normalizedRole,
            'currentPath'   => $request->getPath(),
            'article'       => $article,
            'reviewMode'    => 'pending',
            'backPath'      => '/editor/pending-submissions',
            'primaryAction' => 'approve',
        ]);
    }

    public function reviewApprovedSubmission(Request $request, Response $response): void
    {
        $userInfo = $this->resolveWebUser($request);
        $normalizedRole = strtolower((string)($userInfo['role_name'] ?? ''));
        $this->ensureWebRole($normalizedRole, 'editor');

        $articleId = (int)$request->getRouteParam('id', 0);
        if ($articleId <= 0) {
            header("Location: " . url('/editor/approved-articles'));
            exit;
        }

        $article = $this->article->getApprovedArticleForEditor($articleId, (int)$userInfo['id']);
        if (!$article) {
            header("Location: " . url('/editor/approved-articles'));
            exit;
        }

        $this->setLayout('dashboard');

        echo $this->render('dashboard/editor_review_submission', [
            'pageTitle'     => 'Review Approved Article',
            'pageSubtitle'  => 'Perform a final check before publishing or rejecting.',
            'userName'      => $userInfo['name'],
            'userInitials'  => $this->buildInitials($userInfo['name']),
            'userEmail'     => $userInfo['email'],
            'userRole'      => $normalizedRole,
            'currentPath'   => $request->getPath(),
            'article'       => $article,
            'reviewMode'    => 'approved',
            'backPath'      => '/editor/approved-articles',
            'primaryAction' => 'publish',
        ]);
    }

    public function selectSubmission(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $userInfo = $this->resolveApiUser($request, $response);
        $this->ensureApiRole($userInfo, 'editor', $response);

        $articleId = $this->extractArticleId($request->getBody());
        if ($articleId <= 0) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $isSelected = $this->article->assignSubmittedToEditor($articleId, (int)$userInfo['id']);
        if (!$isSelected) {
            $response->json([
                'error' => 'This article is no longer available in submitted queue.',
            ], 409);
        }

        $response->json([
            'success' => true,
            'message' => 'Article selected and moved to pending queue.',
        ]);
    }

    public function approveSubmission(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $userInfo = $this->resolveApiUser($request, $response);
        $this->ensureApiRole($userInfo, 'editor', $response);

        $articleId = $this->extractArticleId($request->getBody());
        if ($articleId <= 0) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $approved = $this->article->approvePendingByEditor($articleId, (int)$userInfo['id']);
        if (!$approved) {
            $response->json([
                'error' => 'Unable to approve. The article may not be assigned to you anymore.',
            ], 409);
        }

        $response->json([
            'success' => true,
            'message' => 'Submission approved successfully.',
        ]);
    }

    public function rejectSubmission(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $userInfo = $this->resolveApiUser($request, $response);
        $this->ensureApiRole($userInfo, 'editor', $response);

        $articleId = $this->extractArticleId($request->getBody());
        if ($articleId <= 0) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $rejected = $this->article->rejectPendingByEditor($articleId, (int)$userInfo['id']);
        if (!$rejected) {
            $response->json([
                'error' => 'Unable to reject. The article may not be assigned to you anymore.',
            ], 409);
        }

        $response->json([
            'success' => true,
            'message' => 'Submission rejected successfully.',
        ]);
    }

    public function publishApprovedSubmission(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $userInfo = $this->resolveApiUser($request, $response);
        $this->ensureApiRole($userInfo, 'editor', $response);

        $articleId = $this->extractArticleId($request->getBody());
        if ($articleId <= 0) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $published = $this->article->publishApprovedByEditor($articleId, (int)$userInfo['id']);
        if (!$published) {
            $response->json([
                'error' => 'Unable to publish. The article may not be approved for you anymore.',
            ], 409);
        }

        $response->json([
            'success' => true,
            'message' => 'Article published successfully.',
        ]);
    }

    public function rejectApprovedSubmission(Request $request, Response $response): void
    {
        if ($request->getMethod() !== 'post') {
            $response->json(['error' => 'Method not allowed.'], 405);
        }

        $userInfo = $this->resolveApiUser($request, $response);
        $this->ensureApiRole($userInfo, 'editor', $response);

        $articleId = $this->extractArticleId($request->getBody());
        if ($articleId <= 0) {
            $response->json(['error' => 'A valid article_id is required.'], 422);
        }

        $rejected = $this->article->rejectApprovedByEditor($articleId, (int)$userInfo['id']);
        if (!$rejected) {
            $response->json([
                'error' => 'Unable to reject. The article may not be approved for you anymore.',
            ], 409);
        }

        $response->json([
            'success' => true,
            'message' => 'Approved article rejected successfully.',
        ]);
    }

    private function buildInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }
            $initials .= strtoupper($word[0]);
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials !== '' ? $initials : 'U';
    }

    private function resolveWebUser(Request $request): array
    {
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        $tokenData = $this->token->findValid((string)$tokenStr);

        if (!$tokenData) {
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        $userInfo = $this->user->findById((int)$tokenData['user_id']);
        if (!$userInfo) {
            setcookie('auth_token', '', time() - 3600, '/');
            header("Location: " . url('/auth'));
            exit;
        }

        return $userInfo;
    }

    private function resolveApiUser(Request $request, Response $response): array
    {
        $tokenStr = $_COOKIE['auth_token'] ?? $request->getBearerToken();
        if (!$tokenStr) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        $tokenData = $this->token->findValid((string)$tokenStr);
        if (!$tokenData) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        $userInfo = $this->user->findById((int)$tokenData['user_id']);
        if (!$userInfo) {
            $response->json(['error' => 'Unauthorized'], 401);
        }

        return $userInfo;
    }

    private function ensureWebRole(string $currentRole, string $requiredRole): void
    {
        if ($currentRole !== $requiredRole) {
            header("Location: " . url('/dashboard'));
            exit;
        }
    }

    private function ensureApiRole(array $userInfo, string $requiredRole, Response $response): void
    {
        $roleName = strtolower((string)($userInfo['role_name'] ?? ''));
        if ($roleName !== $requiredRole) {
            $response->json(['error' => 'Forbidden'], 403);
        }
    }

    private function extractArticleId(array $payload): int
    {
        $rawArticleId = $payload['article_id'] ?? null;
        $articleId = filter_var($rawArticleId, FILTER_VALIDATE_INT);

        if ($articleId === false || (int)$articleId <= 0) {
            return 0;
        }

        return (int)$articleId;
    }
}
