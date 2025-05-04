<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Strategy\Random;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MoveHandler extends AbstractHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parser = GameStateParserFactory::make();
        $state = $parser->parse($request->getParsedBody());

        $strategy = new Random();

        $direction = $strategy($state);

        return $this->createJsonResponse([
            'move' => $direction->value,
            'shout' => 'Randomly moving ' . $direction->value . '!',
        ]);
    }
} 