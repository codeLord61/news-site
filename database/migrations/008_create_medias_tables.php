<?php

use app\core\Migration;

class CreateMediasTables extends Migration {

    public function up(PDO $pdo)
    {
        // Medias table
        $sql1 = "CREATE TABLE IF NOT EXISTS `medias` (
                `id` int NOT NULL AUTO_INCREMENT,
                `file_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `media_type` enum('image') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
                `uploaded_by` int DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `fk_media_user` (`uploaded_by`),
                CONSTRAINT `fk_media_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        // Articles-medias pivot table
        $sql2 = "CREATE TABLE IF NOT EXISTS `articles_medias` (
                `article_id` int NOT NULL,
                `media_id` int NOT NULL,
                PRIMARY KEY (`article_id`,`media_id`),
                KEY `fk_articles_medias_media` (`media_id`),
                CONSTRAINT `fk_articles_medias_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_articles_medias_media` FOREIGN KEY (`media_id`) REFERENCES `medias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";

        $pdo->exec($sql1);
        $pdo->exec($sql2);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS `articles_medias`");
        $pdo->exec("DROP TABLE IF EXISTS `medias`");
    }
}