<?php

namespace BattleSnake\Strategy;

use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\MoveClassifier;
use BattleSnake\Analyzer\SafeMove;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;

class Random
{
    public function __invoke(GameState $state): Direction
    {
        $classification = new MoveClassifier();
        $moves = $classification($state);

        $safe = [];
        $food = [];
        $least = [];
        $best = [];

        foreach ($moves as $move) {
            if ($move->is(Classification::SAFE)) {
                $safe[] = $move;
            }

            if ($move->is(Classification::FOOD)) {
                $food[] = $move;
            }

            if ($move->is(Classification::FOOD) && $move->is(Classification::SAFE)) {
                $best[] = $move;
            }

            if (!$move->is(Classification::END)) {
                $least[] = $move;
            }
        }

        $set = $moves;;

        if (!empty($best)) {
            $set = $best;
        } elseif (!empty($safe)) {
            $set = $safe;
        } elseif (!empty($least)) {
            $set = $least;
        }

        $set = iterator_to_array($set);

        $random_index = array_rand($set);
        return $set[$random_index]->direction;
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