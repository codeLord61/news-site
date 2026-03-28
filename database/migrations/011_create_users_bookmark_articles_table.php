<?php

use app\core\Migration;

class CreateUsersBookmarkArticlesTable extends Migration
{
    /**
     * Input: PDO connection instance.
     * Output: void. Executes CREATE TABLE query.
     */
    public function up(PDO $pdo): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `users_bookmark_articles` (
                `user_id` int NOT NULL,
                `article_id` int NOT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`user_id`, `article_id`),
                KEY `fk_bookmark_article` (`article_id`),
                CONSTRAINT `fk_bookmark_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_bookmark_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";
        
        $pdo->exec($sql);
    }

    /**
     * Input: PDO connection instance.
     * Output: void. Executes DROP TABLE query.
     */
    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS `users_bookmark_articles`;");
    }
}
