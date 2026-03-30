<?php

use app\core\Migration;

class CreateNotificationsTable extends Migration 
{
    /**
     * Input: PDO connection instance.
     * Output: void. Executes CREATE TABLE query.
     */
    public function up(PDO $pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `notifications` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` int NOT NULL,
            `message` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            `link` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            `is_read` bool NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_notifications_users` (`user_id`),
            CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $pdo->exec($sql);
        
    }

    /**
     * Input: PDO connection instance.
     * Output: void. Executes DROP TABLE query.
     */
    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS `notifications`;");
    }
}