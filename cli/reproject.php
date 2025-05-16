<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BattleSnake\ApplicationFactory;

$app = new ApplicationFactory()->make();

$listener = new BattleSnake\Projection\GameStatListener(
    $app->entity_manager,
    $app->root_repository,
);

$app->connection->executeStatement("TRUNCATE TABLE game_stats");

foreach($app->event_repository->getAll() as $payload) {
    $listener($payload);
}