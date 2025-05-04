<?php

namespace BattleSnake;

use BattleSnake\Core\Application;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;

class ApplicationFactory
{
    public function make(): Application
    {
        return new Application(
            response_factory: new ResponseFactory(),
            server_request_factory: new ServerRequestFactory(),
            stream_factory: new StreamFactory()
        );
    }
}