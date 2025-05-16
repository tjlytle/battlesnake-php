<?php

namespace BattleSnake\Projection;

use BattleSnake\Domain\GameState;
use BattleSnake\Event\End;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Root\RootRepository;
use Doctrine\ORM\EntityManager;

class GameStatListener
{
    public function __construct(
        private readonly EntityManager $entity_manager,
        private readonly RootRepository $root_repository,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $event = $payload->event;

        if (!($event instanceof End)) {
            return;
        }

        $aggregate_id = $payload->uuid;
        $win = $this->determineWin($event->game);

        $game = $this->root_repository->retrieve($aggregate_id);

        $game_stat = new GameStat(
            $aggregate_id,
            $win,
            $event->game->turn,
            $game->getTurn()
        );

        $this->entity_manager->persist($game_stat);
        $this->entity_manager->flush();
    }

    private function determineWin(GameState $game): bool
    {
        $you = $game->you;

        // Check if our snake is still alive at the end of the game
        foreach ($game->board->snakes as $snake) {
            if ($snake->id === $you->id) {
                return true; // Our snake is still alive, so we won
            }
        }

        return false; // Our snake is not in the list of remaining snakes
    }
}
