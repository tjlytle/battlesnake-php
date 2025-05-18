<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\Turn;
use BattleSnake\Root\SnapshotRepository;
use BattleSnake\Strategy\Strategy;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class MoveHandler extends AbstractHandler
{
    public function __construct(
        protected readonly ResponseFactoryInterface $response_factory,
        protected readonly SnapshotRepository $repository,
        protected readonly Strategy $strategy,
    )
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parser = GameStateParserFactory::make();
        $state = $parser->parse($request->getParsedBody());

        $game_id = Uuid::fromString($state->game->id);

        $root = $this->repository->retrieveFromSnapshot($game_id);
        $root->addEvent(new Turn($state));
        $this->repository->persist($root);

        if ($root->getVersion() % 5 === 0) {
            $this->repository->snapshot($root);
        }

        $direction = ($this->strategy)($state);

        return $this->createJsonResponse([
            'move' => $direction->value,
            'shout' => 'Randomly moving ' . $direction->value . '!',
        ]);
    }
} 