<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\App;
use app\core\Database;

// Boot the application simply to load environment variables and set root path
$app = new App(dirname(__DIR__));

$db = new Database();
$db->applyMigrations();
