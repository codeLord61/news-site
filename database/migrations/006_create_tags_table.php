<?php

use app\core\Migration;

class CreateTagsTable extends Migration{
    public function up(PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `tags` (
                `id` int NOT NULL AUTO_INCREMENT,
                `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`),
                UNIQUE KEY `slug` (`slug`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS tags");
    }
}