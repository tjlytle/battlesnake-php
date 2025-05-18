<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Board
{
    public function __construct(
        public int $height,
        public int $width,
        public CoordinateCollection $food = new CoordinateCollection(),
        public CoordinateCollection $hazards = new CoordinateCollection(),
        public BattlesnakeCollection $snakes = new BattlesnakeCollection(),
    ) {
    }
}
