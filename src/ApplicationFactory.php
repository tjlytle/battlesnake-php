<?php

namespace BattleSnake;

use BattleSnake\Core\Application;
use Laminas\Diactoros\ResponseFactory;

class ApplicationFactory
{
    public function make(): Application
    {
        return new Application(
            response_factory: new ResponseFactory(),
            config: require(__DIR__ . '/../config/config.php'),
        );
    }
}