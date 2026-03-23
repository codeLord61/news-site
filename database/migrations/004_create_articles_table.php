<?php

use app\core\Migration;

class CreateArticlesTable extends Migration {
    
    public function up(PDO $pdo){
        // query
        $sql = "CREATE TABLE IF NOT EXISTS `articles` (
                `id` int NOT NULL AUTO_INCREMENT,
                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `excerpt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                `status` enum('draft','submitted','pending','approved','rejected','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `reporter_id` int DEFAULT NULL,
                `managed_by` int DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                `approved_at` timestamp NULL DEFAULT NULL,
                `published_at` timestamp NULL DEFAULT NULL,
                `scheduled_publish_time` timestamp NULL DEFAULT NULL,
                `view_count` int DEFAULT '0',
                `share_count` int DEFAULT '0',
                `deleted_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `fk_articles_reporter` (`reporter_id`),
                KEY `fk_articles_editor` (`managed_by`),
                FULLTEXT KEY `ft_articles_search` (`title`,`content`),
                CONSTRAINT `fk_articles_editor` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk_articles_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $pdo->exec($sql);

    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS `articles`;");

    }
}
