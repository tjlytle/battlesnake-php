<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Board
{
    public function __construct(
        public int $height,
        public int $width,
        public CoordinateCollection $food,
        public CoordinateCollection $hazards,
        public BattlesnakeCollection $snakes,
    ) {
    }
}