<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Coordinate
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}
