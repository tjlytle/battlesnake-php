<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Battlesnake
{
    public function __construct(
        public string $id,
        public string $name,
        public int $health,
        public CoordinateCollection $body = new CoordinateCollection(),
        public string $latency = '',
        public Coordinate|null $head = null,
        public int $length = 0,
        public string $shout = '',
        public string $squad = '',
        public Customizations $customizations = new Customizations(),
    ) {
    }
}
