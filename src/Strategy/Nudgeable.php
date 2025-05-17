<?php

namespace BattleSnake\Strategy;

use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\MoveClassifier;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;
use BattleSnake\Root\Repository;
use Ramsey\Uuid\Uuid;

class Nudgeable implements Strategy
{
    public function __construct(
        private readonly Repository $repository,
        private readonly Strategy $fallbackStrategy,
        private readonly MoveClassifier $classifier,
    ) {
    }

    public function __invoke(GameState $game_state): Direction
    {
        $game = $this->repository->retrieve(Uuid::fromString($game_state->game->id));
        $nudgeDirection = $game->getLastNudge();

        if ($nudgeDirection === null) {
            return ($this->fallbackStrategy)($game_state);
        }

        $classifications = ($this->classifier)($game_state);
        foreach ($classifications as $classification) {
            if ($classification->direction !== $nudgeDirection) {
                continue;
            }

            if ($classification->is(Classification::SAFE)) {
                return $classification->direction;
            }
            return ($this->fallbackStrategy)($game_state);
        }

    }
}
