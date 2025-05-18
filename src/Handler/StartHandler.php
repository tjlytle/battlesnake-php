<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\Start;
use BattleSnake\Root\RootRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class StartHandler extends AbstractHandler
{
    public function __construct(
        protected readonly ResponseFactoryInterface $response_factory,
        protected readonly RootRepository $repository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parser = GameStateParserFactory::make();
        $state = $parser->parse($request->getParsedBody());

        $game_id = Uuid::fromString($state->game->id);

        $root = $this->repository->retrieve($game_id);
        $root->addEvent(new Start($state));
        $this->repository->persist($root);

        return $this->response_factory->createResponse(204);
    }
}
