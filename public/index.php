<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use BattleSnake\Core\Application;

// TODO: pick a PSR-17 implementation
$app = new Application(
    response_factory: new ResponseFactory(),
    server_request_factory: new ServerRequestFactory(),
    stream_factory: new StreamFactory()
);

$app->run(); 