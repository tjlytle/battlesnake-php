<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Board
{
    /**
     * @param Coordinate[] $food
     * @param Coordinate[] $hazards
     * @param Battlesnake[] $snakes
     */
    public function __construct(
        public int $height,
        public int $width,
        public array $food,
        public array $hazards,
        public array $snakes,
    ) {
    }
}