<?php

declare(strict_types=1);

namespace BattleSnake\Domain;

final readonly class Customizations
{
    public function __construct(
        public ?string $color = null,
        public ?string $head = null,
        public ?string $tail = null,
    ) {
    }
}
