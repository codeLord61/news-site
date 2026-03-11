<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Request;
use app\core\Response;

class TagController extends Controller
{
    /**
     * GET /api/v1/tags
     * List all tags with article counts.
     */
    public function index(Request $request, Response $response)
    {
        $db = App::$app->db;

        $sql = "SELECT t.id, t.name, t.slug,
                       (SELECT COUNT(DISTINCT at2.article_id)
                        FROM articles_tags at2
                        INNER JOIN articles a ON at2.article_id = a.id
                        WHERE at2.tag_id = t.id
                          AND a.status = 'published'
                          AND a.deleted_at IS NULL
                       ) AS article_count
                FROM tags t
                ORDER BY t.name ASC";

        $stmt = $db->pdo->query($sql);
        $tags = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->json(['data' => $tags]);
    }

    /**
     * GET /api/v1/tags/{slug}
     * Get articles by a specific tag (paginated).
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (!$slug) {
            $response->json(['error' => 'Tag slug is required'], 400);
        }

        $db = App::$app->db;

        // Fetch tag
        $tagStmt = $db->pdo->prepare("SELECT id, name, slug FROM tags WHERE slug = ?");
        $tagStmt->execute([$slug]);
        $tag = $tagStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$tag) {
            $response->json(['error' => 'Tag not found'], 404);
        }

        // Pagination
        $params = $request->getQueryParams();
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(50, max(1, (int)($params['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;

        // Count articles with this tag
        $countSql = "SELECT COUNT(DISTINCT a.id)
                     FROM articles a
                     INNER JOIN articles_tags at2 ON a.id = at2.article_id
                     WHERE at2.tag_id = ?
                       AND a.status = 'published'
                       AND a.deleted_at IS NULL";
        $countStmt = $db->pdo->prepare($countSql);
        $countStmt->execute([$tag['id']]);
        $total = (int)$countStmt->fetchColumn();

        // Fetch articles
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                INNER JOIN articles_tags at2 ON a.id = at2.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE at2.tag_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $db->pdo->prepare($sql);
        $stmt->bindValue(1, $tag['id'], \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['reporter'] = [
                'name' => $row['reporter_name'],
                'username' => $row['reporter_username'],
            ];
            unset($row['reporter_name'], $row['reporter_username']);
            $articles[] = $row;
        }

        $response->json([
            'data' => [
                'tag' => $tag,
                'articles' => $articles,
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => (int)ceil($total / $limit),
            ],
        ]);
    }
}
