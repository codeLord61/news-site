<?php

namespace app\models;

use app\core\Model;

class Media extends Model
{
    /**
     * Get the first media (thumbnail) linked to an article.  
     * 
     * @return array|null ['id', 'file_url', 'alt_text', 'caption'] or null
     */
    public function getFirstForArticle(int $articleId): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT m.id, m.file_url, m.alt_text, m.caption
             FROM medias m
             INNER JOIN articles_medias am ON m.id = am.media_id
             WHERE am.article_id = ?
             ORDER BY m.id ASC
             LIMIT 1"
        );
        $stmt->execute([$articleId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }    
}