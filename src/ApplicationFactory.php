<?php

declare(strict_types=1);

namespace BattleSnake;

use BattleSnake\Core\Application;
use Laminas\Diactoros\ResponseFactory;

class ApplicationFactory
{
    public function make(): Application
    {
        return new Application(
            response_factory: new ResponseFactory(),
            config: include __DIR__ . '/../config/config.php',
        );
    }
}
