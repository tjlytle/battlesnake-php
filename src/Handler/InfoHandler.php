<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InfoHandler extends AbstractHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->createJsonResponse(
            [
            'apiversion' => '1',
            'author' => 'totally_not_ai',
            'color' => '#FF0000',
            'head' => 'default',
            'tail' => 'default',
            'version' => '0.0.1',
            ],
        );
    }
}
