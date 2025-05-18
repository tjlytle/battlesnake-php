<?php

declare(strict_types=1);

namespace BattleSnake\Root;

use Ramsey\Uuid\UuidInterface;

interface Repository
{
    public function retrieve(UuidInterface $aggregate_id): Game;

    public function persist(AggregateRoot $root): void;
}
