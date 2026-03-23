<?php

use app\core\Migration;

class AddIsThumbnailToMedias extends Migration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            "ALTER TABLE `medias`
             ADD COLUMN `is_thumbnail` tinyint(1) NOT NULL DEFAULT 0
             AFTER `alt_text`"
        );
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("ALTER TABLE `medias` DROP COLUMN `is_thumbnail`");
    }
}
