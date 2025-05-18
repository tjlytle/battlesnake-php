<?php

declare(strict_types=1);

namespace BattleSnake\Root;

use BattleSnake\Eventsource\Event;
use Ramsey\Uuid\UuidInterface;

interface AggregateRoot
{
    public function getVersion(): int;

    public function getAggregateRootId(): UuidInterface;

    public function loadEvents(Event ...$events): void;

    public function drainEventBuffer(): array;
}
