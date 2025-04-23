<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Game
{
    public function __construct(
        public string $id,
        public Ruleset $ruleset,
        public ?string $map = null,
        public ?string $source = null,
        public ?int $timeout = null,
    ) {
    }
}
