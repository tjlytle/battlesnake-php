<?php

declare(strict_types=1);

namespace BattleSnake\Eventsource;

use Ramsey\Uuid\UuidInterface;

readonly class Payload
{
    public function __construct(
        public UuidInterface $uuid,
        public int $version,
        public Event $event,
        public \DateTimeImmutable $timestamp,
    ) {
    }
}
