<?php

namespace BattleSnake\Eventsource;

use Ramsey\Uuid\UuidInterface;

readonly class Payload
{
    public function __construct(
        public UuidInterface $uuid,
        public \DateTimeImmutable $timestamp,
        public Event $event,
    ){}
}