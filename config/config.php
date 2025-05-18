<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BattleSnake\Core\Config;

return new Config(
    dsn: 'mysql://battlesnake:battlesnake@mysql/battlesnake'
);