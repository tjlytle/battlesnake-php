<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\Start;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Eventsource\Repository;
use BattleSnake\Strategy\Random;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class MoveHandler extends AbstractHandler
{
    public function __construct(
        protected readonly ResponseFactoryInterface $response_factory,
        protected readonly Repository $repository,
    )
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parser = GameStateParserFactory::make();
        $state = $parser->parse($request->getParsedBody());

        $game_id = Uuid::fromString($state->game->id);

        $past_events = $this->repository->get($game_id);

        $this->repository->persist(new Payload(
            $game_id,
            count($past_events),
            new Start($state),
            new \DateTimeImmutable(),
        ));

        $strategy = new Random();

        $direction = $strategy($state);

        return $this->createJsonResponse([
            'move' => $direction->value,
            'shout' => 'Randomly moving ' . $direction->value . '!',
        ]);
    }
} 