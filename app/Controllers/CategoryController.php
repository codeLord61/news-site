<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Request;
use app\core\Response;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/categories
     * List all categories with parent_id for hierarchy building.
     */
    public function index(Request $request, Response $response)
    {
        $db = App::$app->db;

        $sql = "SELECT c.id, c.name, c.slug, c.description, c.parent_id,
                       (SELECT COUNT(DISTINCT ac.article_id)
                        FROM articles_categories ac
                        INNER JOIN articles a ON ac.article_id = a.id
                        WHERE ac.category_id = c.id
                          AND a.status = 'published'
                          AND a.deleted_at IS NULL
                       ) AS article_count
                FROM categories c
                ORDER BY c.name ASC";

        $stmt = $db->pdo->query($sql);
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->json(['data' => $categories]);
    }

    /**
     * GET /api/v1/categories/{slug}
     * Get a single category with its paginated articles.
     */
    public function show(Request $request, Response $response)
    {
        $slug = $request->getRouteParam('slug');

        if (!$slug) {
            $response->json(['error' => 'Category slug is required'], 400);
        }

        $db = App::$app->db;

        // Fetch category
        $catStmt = $db->pdo->prepare("SELECT id, name, slug, description, parent_id FROM categories WHERE slug = ?");
        $catStmt->execute([$slug]);
        $category = $catStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$category) {
            $response->json(['error' => 'Category not found'], 404);
        }

        // Pagination
        $params = $request->getQueryParams();
        $page = max(1, (int)($params['page'] ?? 1));
        $limit = min(50, max(1, (int)($params['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;

        // Count articles in this category
        $countSql = "SELECT COUNT(DISTINCT a.id)
                     FROM articles a
                     INNER JOIN articles_categories ac ON a.id = ac.article_id
                     WHERE ac.category_id = ?
                       AND a.status = 'published'
                       AND a.deleted_at IS NULL";
        $countStmt = $db->pdo->prepare($countSql);
        $countStmt->execute([$category['id']]);
        $total = (int)$countStmt->fetchColumn();

        // Fetch articles
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $db->pdo->prepare($sql);
        $stmt->bindValue(1, $category['id'], \PDO::PARAM_INT);
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

        // Include child categories if any
        $childStmt = $db->pdo->prepare("SELECT id, name, slug FROM categories WHERE parent_id = ?");
        $childStmt->execute([$category['id']]);
        $category['children'] = $childStmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->json([
            'data' => [
                'category' => $category,
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
