<?php

declare(strict_types=1);

namespace BattleSnake\Handler;

use BattleSnake\Command\Nudge;
use BattleSnake\Domain\Direction;
use BattleSnake\Root\InvalidState;
use BattleSnake\Root\RootRepository;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class ManualHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseFactory $response_factory,
        private readonly RootRepository $root_repository,
    ) {
    }

    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (! isset($body['game_id'])) {
            return $this->response_factory->createResponse(400);
        }

        try {
            $game_id = Uuid::fromString($body['game_id']);
        } catch (\InvalidArgumentException $e) {
            return $this->response_factory->createResponse(404);
        }

        $game = $this->root_repository->retrieve($game_id);
        $direction = Direction::tryFrom($body['direction'] ?? '');

        if (! $direction) {
            return $this->response_factory->createResponse(400);
        }

        try {
            $game->process(new Nudge($direction));
        } catch (InvalidState $e) {
            return $this->response_factory->createResponse(400);
        }

        $this->root_repository->persist($game);
        return $this->response_factory->createResponse(202);
    }
}
