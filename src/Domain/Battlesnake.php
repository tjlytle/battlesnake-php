<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Battlesnake
{
    /**
     * @param Coordinate[] $body
     * @param array<string, mixed> $customizations
     */
    public function __construct(
        public string $id,
        public string $name,
        public int $health,
        public array $body,
        public string $latency = '',
        public ?Coordinate $head = null,
        public int $length = 0,
        public string $shout = '',
        public string $squad = '',
        public array $customizations = []
    ) {

    }
}
