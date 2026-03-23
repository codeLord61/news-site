<?php

use app\core\Migration;

class CreateCommentsTable extends Migration
{

    public function up(PDO $pdo): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `comments` (
                `id` int NOT NULL AUTO_INCREMENT,
                `article_id` int NOT NULL,
                `user_id` int NOT NULL,
                `managed_by` int DEFAULT NULL,
                `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                `is_approved` tinyint(1) NOT NULL DEFAULT '0',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `fk_comments_article` (`article_id`),
                KEY `fk_comments_user` (`user_id`),
                KEY `fk_comments_editor` (`managed_by`),
                CONSTRAINT `fk_comments_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_comments_editor` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        
        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS `comments`;");
    }
}
