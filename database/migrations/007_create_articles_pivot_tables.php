<?php

use app\core\Migration;

class CreateArticlesPivotTables extends Migration {
    
    public function up(PDO $pdo)
    {
        // Article categories
        $sql1 = "CREATE TABLE IF NOT EXISTS `articles_categories` (
                `article_id` int NOT NULL,
                `category_id` int NOT NULL,
                PRIMARY KEY (`article_id`,`category_id`),
                KEY `fk_articles_categories_category` (`category_id`),
                CONSTRAINT `fk_articles_categories_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_articles_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
        // Article tags
        $sql2 = "CREATE TABLE IF NOT EXISTS `articles_tags` (
                `article_id` int NOT NULL,
                `tag_id` int NOT NULL,
                PRIMARY KEY (`article_id`,`tag_id`),
                KEY `fk_articles_tags_tag` (`tag_id`),
                CONSTRAINT `fk_articles_tags_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_articles_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

        $pdo->exec($sql1);
        $pdo->exec($sql2);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS articles_categories");
        $pdo->exec("DROP TABLE IF EXISTS articles_tags");
    }
    
}