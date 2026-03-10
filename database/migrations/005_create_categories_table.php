<?php

use app\core\Migration;

class CreateCategoriesTable extends Migration {

    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `categories` (
                `id` int NOT NULL AUTO_INCREMENT,
                `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                `parent_id` int DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`),
                UNIQUE KEY `slug` (`slug`),
                CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS `categories`;");
    }
}