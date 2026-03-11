<?php
namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Request;
use app\core\Response;

class ArticleController extends Controller
{
    // GET /api/v1/articles
    public function index(Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        // Pagination
        $page   = max(1, (int) ($params['page'] ?? 1));
        $limit  = min(50, max(1, (int) ($params['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;
        $category = $params['category'] ?? null;
        $tag      = $params['tag'] ?? null;

        $db = App::$app->db;

        // Query
        $baseSelect = "SELECT DISTINCT a.id, a.title, a.slug, a.excerpt, a.status,
                    a.published_at, a.view_count, a.share_count,
                    u.name AS reporter_name, u.username AS reporter_username";
        $baseFrom = " FROM articles a
                    LEFT JOIN users u ON a.reporter_id = u.id";
        $conditions = " WHERE a.status = 'published' AND a.deleted_at IS NULL";
        $bindParams = [];

        // Category filter
        if ($category) {
            $baseFrom .= " INNER JOIN articles_categories ac ON a.id = ac.article_id
                        INNER JOIN categories c ON ac.category_id = c.id";
            $conditions   .= " AND c.slug = ?";
            $bindParams[]  = $category;
        }

        // Tag filter
        if ($tag) {
            $baseFrom .= " INNER JOIN articles_tags at2 ON a.id = at2.article_id
                        INNER JOIN tags t ON at2.tag_id = t.id";
            $conditions   .= " AND t.slug = ?";
            $bindParams[]  = $tag;
        }

        $orderBy = " ORDER BY a.published_at DESC";

        // Count total
        $countSql  = "SELECT COUNT(DISTINCT a.id)" . $baseFrom . $conditions;
        $countStmt = $db->pdo->prepare($countSql);
        $countStmt->execute($bindParams);
        $total = (int) $countStmt->fetchColumn();

        // Fetch Articles
        $sql  = $baseSelect . $baseFrom . $conditions . $orderBy . " LIMIT ? OFFSET ?";
        $stmt = $db->pdo->prepare($sql);

        $i = 1;
        foreach ($bindParams as $val) {
            $stmt->bindValue($i++, $val);
        }
        $stmt->bindValue($i++, $limit, \PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    
        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($db, $row['id']);
            $row['tags']       = $this->getTagsForArticle($db, $row['id']);
            $row['reporter']   = [
                'name'     => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        $response->json([
            'data'       => $articles,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $limit,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $limit),
            ],
        ]);
    }

    // GET /api/v1/articles/{slug}
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (! $slug) {
            $response->json(['error' => 'Article slug is required'], 400);
        }

        $db = App::$app->db;

        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.status,
                       a.published_at, a.view_count, a.share_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.slug = ? AND a.status = 'published' AND a.deleted_at IS NULL";

        $stmt = $db->pdo->prepare($sql);
        $stmt->execute([$slug]);
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (! $article) {
            $response->json(['error' => 'Article not found'], 404);
        }

        // Increment view count
        $db->pdo->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")
            ->execute([$article['id']]);
        $article['view_count']++;

        // Enrich with categories, tags, reporter
        $article['categories'] = $this->getCategoriesForArticle($db, $article['id']);
        $article['tags']       = $this->getTagsForArticle($db, $article['id']);
        $article['reporter']   = [
            'name'     => $article['reporter_name'],
            'username' => $article['reporter_username'],
        ];
        unset($article['reporter_name'], $article['reporter_username']);

        $response->json(['data' => $article]);
    }

    private function getCategoriesForArticle($db, int $articleId): array
    {
        $stmt = $db->pdo->prepare(
            "SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN articles_categories ac ON c.id = ac.category_id
             WHERE ac.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTagsForArticle($db, int $articleId): array
    {
        $stmt = $db->pdo->prepare(
            "SELECT t.id, t.name, t.slug
             FROM tags t
             INNER JOIN articles_tags at2 ON t.id = at2.tag_id
             WHERE at2.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}