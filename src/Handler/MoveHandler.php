<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MoveHandler extends AbstractHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->createJsonResponse([
            'move' => 'up',
            'shout' => 'Moving up!'
        ]);
    }
} 