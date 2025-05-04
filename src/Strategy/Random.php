<?php

namespace BattleSnake\Strategy;

use BattleSnake\Analyzer\SafeMove;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;

class Random
{
    public function __invoke(GameState $state): Direction
    {
        $safe_move = new SafeMove();
        $safe_moves = $safe_move($state);
        $directions = $this->getSafeDirections($state, $safe_moves);
        $random_index = array_rand($directions);
        return $directions[$random_index];
    }

    /**
     * @return array<Direction>
     */
    private function getSafeDirections(GameState $state, CoordinateCollection $safe_moves): array
    {
        $directions = [];
        $head = $state->you->head;
        
        if (!$head) {
            return [];
        }
        
        foreach ($safe_moves->coordinates as $coordinate) {
            // Check which direction this coordinate is from the head
            if ($coordinate->x === $head->x && $coordinate->y === $head->y + 1) {
                $directions[] = Direction::UP;
            } elseif ($coordinate->x === $head->x && $coordinate->y === $head->y - 1) {
                $directions[] = Direction::DOWN;
            } elseif ($coordinate->x === $head->x - 1 && $coordinate->y === $head->y) {
                $directions[] = Direction::LEFT;
            } elseif ($coordinate->x === $head->x + 1 && $coordinate->y === $head->y) {
                $directions[] = Direction::RIGHT;
            }
        }
        
        return $directions;
    }
}