<?php

namespace BattleSnake\Root;

use Ramsey\Uuid\UuidInterface;

interface Repository
{
    public function retrieve(UuidInterface $aggregate_id): Game;

    public function persist(AggregateRoot $root): void;
}