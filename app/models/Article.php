<?php

namespace app\models;

use app\core\Model;

class Article extends Model
{
    /**
     * Get paginated published articles with optional category, tag, and search filters.
     *
     * @return array{articles: array, total: int}
     */
    public function getPublished(array $filters, string $sort, int $limit, int $offset): array
    {
        $category = $filters['category'] ?? null;
        $tag = $filters['tag'] ?? null;
        $search = $filters['search'] ?? null;

        $baseSelect = "SELECT DISTINCT a.id, a.title, a.slug, a.excerpt, a.status,
                        a.published_at, a.view_count, a.share_count,
                        u.name AS reporter_name";
        $baseFrom = " FROM articles a
                       LEFT JOIN users u ON a.reporter_id = u.id";
        $conditions = " WHERE a.status = 'published' AND a.deleted_at IS NULL";
        $bindParams = [];

        if ($category) {
            $baseFrom .= " INNER JOIN articles_categories ac ON a.id = ac.article_id
                           INNER JOIN categories c ON ac.category_id = c.id";
            $conditions .= " AND c.slug = ?";
            $bindParams[] = $category;
        }

        if ($tag) {
            $baseFrom .= " INNER JOIN articles_tags at2 ON a.id = at2.article_id
                           INNER JOIN tags t ON at2.tag_id = t.id";
            $conditions .= " AND t.slug = ?";
            $bindParams[] = $tag;
        }

        if ($search) {
            $conditions .= " AND MATCH(a.title, a.content) AGAINST(? IN NATURAL LANGUAGE MODE)";
            $bindParams[] = $search;
        }

        switch ($sort) {
            case 'popular':
                $orderBy = " ORDER BY a.view_count DESC";
                break;
            case 'oldest':
                $orderBy = " ORDER BY a.published_at ASC";
                break;
            default:
                $orderBy = " ORDER BY a.published_at DESC";
        }

        // Count total
        $countSql = "SELECT COUNT(DISTINCT a.id)" . $baseFrom . $conditions;
        $countStmt = $this->db()->prepare($countSql);
        $countStmt->execute($bindParams);
        $total = (int)$countStmt->fetchColumn();

        // Fetch paginated rows
        $sql = $baseSelect . $baseFrom . $conditions . $orderBy . " LIMIT ? OFFSET ?";
        $stmt = $this->db()->prepare($sql);
        $i = 1;
        foreach ($bindParams as $val) {
            $stmt->bindValue($i++, $val);
        }
        $stmt->bindValue($i++, $limit, \PDO::PARAM_INT);
        $stmt->bindValue($i, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Enrich with categories, tags, and nested reporter
        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['tags'] = $this->getTagsForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return ['articles' => $articles, 'total' => $total];
    }

    /**
     * Get trending articles within a time interval.
     */
    public function getTrending(string $interval, int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name, u.username AS reporter_username
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.status = 'published'
                  AND a.deleted_at IS NULL
                  AND a.published_at >= NOW() - INTERVAL $interval
                ORDER BY a.view_count DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['tags'] = $this->getTagsForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Find a single published article by slug.
     */
    public function findBySlug(string $slug): array|false
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.status,
                       a.published_at, a.view_count, a.share_count,
                       u.name AS reporter_name,
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS thumbnail,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS alt_text
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.slug = ? AND a.status = 'published' AND a.deleted_at IS NULL";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([$slug]);
        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            return false;
        }

        $article['categories'] = $this->getCategoriesForArticle($article['id']);
        $article['tags'] = $this->getTagsForArticle($article['id']);
        $article['reporter'] = [
            'name' => $article['reporter_name'],
        ];
        unset($article['reporter_name']);

        return $article;
    }

    /**
     * Increment the view count for an article.
     */
    public function incrementViewCount(int $id): void
    {
        $this->db()->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")
            ->execute([$id]);
    }

    /**
     * Get the latest N published articles (regardless of category).
     * Used for the hero section on the homepage.
     */
    public function getLatest(int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name,
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS thumbnail,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS alt_text
                FROM articles a
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE a.status = 'published' AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Get published articles for a specific category.
     * Used for category sections on the homepage.
     */
    public function getPublishedByCategory(int $categoryId, int $limit): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name,
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS thumbnail,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS alt_text
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Get paginated published articles for a specific category.
     * Used for the dynamic category page.
     */
    public function getPaginatedByCategory(int $categoryId, int $limit, int $offset): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.published_at, a.view_count,
                       u.name AS reporter_name,
                       (SELECT m.file_url FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS thumbnail,
                       (SELECT m.alt_text FROM medias m
                        INNER JOIN articles_medias am ON m.id = am.media_id
                        WHERE am.article_id = a.id AND m.is_thumbnail = 1
                        LIMIT 1) AS alt_text
                FROM articles a
                INNER JOIN articles_categories ac ON a.id = ac.article_id
                LEFT JOIN users u ON a.reporter_id = u.id
                WHERE ac.category_id = ?
                  AND a.status = 'published'
                  AND a.deleted_at IS NULL
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($rows as $row) {
            $row['categories'] = $this->getCategoriesForArticle($row['id']);
            $row['reporter'] = [
                'name' => $row['reporter_name'],
            ];
            unset($row['reporter_name']);
            $articles[] = $row;
        }

        return $articles;
    }

    /**
     * Find an article by ID that belongs to a reporter.
     */
    public function findByIdForReporter(int $articleId, int $reporterId): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT id, title, slug, excerpt, content, status, updated_at
             FROM articles
             WHERE id = ? AND reporter_id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$articleId, $reporterId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all articles created by a reporter.
     */
    public function getReporterArticles(int $reporterId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.status, a.created_at, a.updated_at,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             WHERE a.reporter_id = ? AND a.deleted_at IS NULL
             ORDER BY a.created_at DESC"
        );

        $stmt->execute([$reporterId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get reporter submissions excluding drafts.
     */
    public function getReporterSubmissions(int $reporterId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.status, a.created_at, a.updated_at,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             WHERE a.reporter_id = ?
               AND a.status IN ('submitted', 'pending', 'approved', 'rejected', 'published')
               AND a.deleted_at IS NULL
             ORDER BY a.created_at DESC"
        );

        $stmt->execute([$reporterId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * List all submitted articles for editor pickup.
     */
    public function getSubmittedForEditorQueue(): array
    {
        $stmt = $this->db()->query(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.status, a.created_at,
                    u.name AS reporter_name,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             LEFT JOIN users u ON u.id = a.reporter_id
             WHERE a.status = 'submitted' AND a.deleted_at IS NULL
             ORDER BY a.created_at ASC"
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * List pending articles assigned to a specific editor.
     */
    public function getPendingForEditor(int $editorId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.status, a.created_at, a.updated_at,
                    a.created_at AS submitted_at,
                    a.updated_at AS pending_since,
                    u.name AS reporter_name,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             LEFT JOIN users u ON u.id = a.reporter_id
             WHERE a.status = 'pending'
               AND a.managed_by = ?
               AND a.deleted_at IS NULL
             ORDER BY a.updated_at DESC, a.created_at DESC"
        );

        $stmt->execute([$editorId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a single pending article assigned to an editor.
     */
    public function getPendingArticleForEditor(int $articleId, int $editorId): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.content, a.status, a.created_at, a.updated_at,
                    a.created_at AS submitted_at,
                    u.name AS reporter_name,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             LEFT JOIN users u ON u.id = a.reporter_id
             WHERE a.id = ?
               AND a.managed_by = ?
               AND a.status = 'pending'
               AND a.deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * List approved articles assigned to a specific editor.
     */
    public function getApprovedForEditor(int $editorId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.status, a.created_at, a.updated_at, a.approved_at,
                    u.name AS reporter_name,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             LEFT JOIN users u ON u.id = a.reporter_id
             WHERE a.status = 'approved'
               AND a.managed_by = ?
               AND a.deleted_at IS NULL
             ORDER BY a.approved_at DESC, a.updated_at DESC, a.created_at DESC"
        );

        $stmt->execute([$editorId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a single approved article assigned to an editor.
     */
    public function getApprovedArticleForEditor(int $articleId, int $editorId): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.content, a.status, a.created_at, a.updated_at, a.approved_at,
                    u.name AS reporter_name,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             LEFT JOIN users u ON u.id = a.reporter_id
             WHERE a.id = ?
               AND a.managed_by = ?
               AND a.status = 'approved'
               AND a.deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get an article payload used to prefill the reporter editor form.
     */
    public function getReporterArticleForForm(int $articleId, int $reporterId): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.content, a.status, a.created_at, a.updated_at,
                    (SELECT ac.category_id
                     FROM articles_categories ac
                     WHERE ac.article_id = a.id
                     LIMIT 1) AS category_id,
                    (SELECT at2.tag_id
                     FROM articles_tags at2
                     WHERE at2.article_id = a.id
                     LIMIT 1) AS tag_id,
                    (SELECT m.id
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail_media_id,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail_image_url,
                    (SELECT m.alt_text
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail_alt_text,
                    (SELECT m.caption
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail_caption
             FROM articles a
             WHERE a.id = ?
               AND a.reporter_id = ?
               AND a.deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute([$articleId, $reporterId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get reporter-owned article data for dashboard preview page.
     */
    public function getReporterArticlePreview(int $articleId, int $reporterId): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT a.id, a.title, a.excerpt, a.slug, a.content, a.status, a.created_at, a.updated_at,
                    (SELECT c.name
                     FROM categories c
                     INNER JOIN articles_categories ac ON ac.category_id = c.id
                     WHERE ac.article_id = a.id
                     ORDER BY c.name ASC
                     LIMIT 1) AS category_name,
                    (SELECT GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ')
                     FROM tags t
                     INNER JOIN articles_tags at2 ON at2.tag_id = t.id
                     WHERE at2.article_id = a.id) AS tag_names,
                    (SELECT m.file_url
                     FROM medias m
                     INNER JOIN articles_medias am ON am.media_id = m.id
                     WHERE am.article_id = a.id AND m.is_thumbnail = 1
                     LIMIT 1) AS thumbnail
             FROM articles a
             WHERE a.id = ?
               AND a.reporter_id = ?
               AND a.deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute([$articleId, $reporterId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Soft-delete a reporter-owned article.
     */
    public function deleteReporterArticle(int $articleId, int $reporterId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET deleted_at = NOW(), updated_at = NOW()
             WHERE id = ?
               AND reporter_id = ?
               AND deleted_at IS NULL"
        );

        $stmt->execute([$articleId, $reporterId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Move an article from submitted queue to pending for a specific editor.
     */
    public function assignSubmittedToEditor(int $articleId, int $editorId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET status = 'pending',
                 managed_by = ?,
                 updated_at = NOW()
             WHERE id = ?
               AND status = 'submitted'
               AND deleted_at IS NULL"
        );

        $stmt->execute([$editorId, $articleId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Approve an article currently assigned to an editor.
     */
    public function approvePendingByEditor(int $articleId, int $editorId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET status = 'approved',
                 approved_at = NOW(),
                 updated_at = NOW()
             WHERE id = ?
               AND managed_by = ?
               AND status = 'pending'
               AND deleted_at IS NULL"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Reject an article currently assigned to an editor.
     */
    public function rejectPendingByEditor(int $articleId, int $editorId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET status = 'rejected',
                 approved_at = NULL,
                 updated_at = NOW()
             WHERE id = ?
               AND managed_by = ?
               AND status = 'pending'
               AND deleted_at IS NULL"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Publish an approved article currently assigned to an editor.
     */
    public function publishApprovedByEditor(int $articleId, int $editorId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET status = 'published',
                 published_at = NOW(),
                 updated_at = NOW()
             WHERE id = ?
               AND managed_by = ?
               AND status = 'approved'
               AND deleted_at IS NULL"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Reject an approved article currently assigned to an editor.
     */
    public function rejectApprovedByEditor(int $articleId, int $editorId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE articles
             SET status = 'rejected',
                 approved_at = NULL,
                 updated_at = NOW()
             WHERE id = ?
               AND managed_by = ?
               AND status = 'approved'
               AND deleted_at IS NULL"
        );

        $stmt->execute([$articleId, $editorId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Create a reporter-owned article with optional category and tag links.
     */
    public function createReporterArticle(int $reporterId, array $payload): int
    {
        $pdo = $this->db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO articles (title, excerpt, slug, content, status, reporter_id, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $payload['title'],
                $payload['excerpt'],
                $payload['slug'],
                $payload['content_html'],
                $payload['status'],
                $reporterId,
            ]);

            $articleId = (int)$pdo->lastInsertId();

            $this->syncArticleCategory($articleId, $payload['category_id'], $pdo);
            $this->syncArticleTag($articleId, $payload['tag_id'], $pdo);
            if (array_key_exists('media_ids', $payload)) {
                $this->syncArticleMedias($articleId, $payload['media_ids'], $pdo);
            }

            $pdo->commit();
            return $articleId;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Update an existing reporter-owned article and sync category/tag links.
     */
    public function updateReporterArticle(int $articleId, int $reporterId, array $payload): bool
    {
        $pdo = $this->db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE articles
                 SET title = ?, excerpt = ?, slug = ?, content = ?, status = ?, updated_at = NOW(), published_at = NULL
                 WHERE id = ? AND reporter_id = ? AND deleted_at IS NULL"
            );

            $stmt->execute([
                $payload['title'],
                $payload['excerpt'],
                $payload['slug'],
                $payload['content_html'],
                $payload['status'],
                $articleId,
                $reporterId,
            ]);

            $this->syncArticleCategory($articleId, $payload['category_id'], $pdo);
            $this->syncArticleTag($articleId, $payload['tag_id'], $pdo);
            if (array_key_exists('media_ids', $payload)) {
                $this->syncArticleMedias($articleId, $payload['media_ids'], $pdo);
            }

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Generate a unique slug for an article title.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreArticleId = null): string
    {
        $slugBase = $this->slugify($title);
        $slug = $slugBase;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreArticleId)) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Determine whether a slug already exists on another non-deleted article.
     */
    public function slugExists(string $slug, ?int $ignoreArticleId = null): bool
    {
        if ($ignoreArticleId !== null) {
            $stmt = $this->db()->prepare(
                "SELECT COUNT(*) FROM articles
                 WHERE slug = ? AND deleted_at IS NULL AND id <> ?"
            );
            $stmt->execute([$slug, $ignoreArticleId]);
        } else {
            $stmt = $this->db()->prepare(
                "SELECT COUNT(*) FROM articles
                 WHERE slug = ? AND deleted_at IS NULL"
            );
            $stmt->execute([$slug]);
        }

        return (int)$stmt->fetchColumn() > 0;
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim((string)$text, '-');

        return $text !== '' ? $text : 'article';
    }

    private function syncArticleCategory(int $articleId, ?int $categoryId, ?\PDO $pdo = null): void
    {
        $conn = $pdo ?? $this->db();
        $conn->prepare("DELETE FROM articles_categories WHERE article_id = ?")->execute([$articleId]);

        if ($categoryId !== null) {
            $conn->prepare(
                "INSERT INTO articles_categories (article_id, category_id) VALUES (?, ?)"
            )->execute([$articleId, $categoryId]);
        }
    }

    private function syncArticleTag(int $articleId, ?int $tagId, ?\PDO $pdo = null): void
    {
        $conn = $pdo ?? $this->db();
        $conn->prepare("DELETE FROM articles_tags WHERE article_id = ?")->execute([$articleId]);

        if ($tagId !== null) {
            $conn->prepare(
                "INSERT INTO articles_tags (article_id, tag_id) VALUES (?, ?)"
            )->execute([$articleId, $tagId]);
        }
    }

    /**
     * Replace all article-media links with the current editor media set.
     *
     * @param int[] $mediaIds
     */
    private function syncArticleMedias(int $articleId, array $mediaIds, ?\PDO $pdo = null): void
    {
        $conn = $pdo ?? $this->db();
        $conn->prepare("DELETE FROM articles_medias WHERE article_id = ?")->execute([$articleId]);

        if (empty($mediaIds)) {
            return;
        }

        $stmt = $conn->prepare(
            "INSERT IGNORE INTO articles_medias (article_id, media_id) VALUES (?, ?)"
        );

        foreach ($mediaIds as $mediaId) {
            $mediaId = (int)$mediaId;
            if ($mediaId <= 0) {
                continue;
            }
            $stmt->execute([$articleId, $mediaId]);
        }
    }

    /**
     * Get categories associated with an article.
     */
    public function getCategoriesForArticle(int $articleId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN articles_categories ac ON c.id = ac.category_id
             WHERE ac.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get tags associated with an article.
     */
    public function getTagsForArticle(int $articleId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT t.id, t.name, t.slug
             FROM tags t
             INNER JOIN articles_tags at2 ON t.id = at2.tag_id
             WHERE at2.article_id = ?"
        );
        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
