<?php
require_once __DIR__.'/../vendor/autoload.php';

use app\core\App;
$app = new App(__DIR__);
App::run();