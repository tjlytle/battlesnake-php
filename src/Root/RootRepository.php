<?php

namespace BattleSnake\Root;

use BattleSnake\Eventsource\EventRepository;
use BattleSnake\Eventsource\Payload;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class RootRepository
{
    public function __construct(
        private readonly EventRepository $event_repository,
    ) {
    }

    public function persist(AggregateRoot $root): void
    {
        $events = $root->drainEventBuffer();

        $payloads = [];

        $version = $root->getVersion();
        foreach ($events as $event) {
            $payloads[] = new Payload($root->getAggregateRootId(), ++$version, $event, new DateTimeImmutable());
        }

        $this->event_repository->persist(...$payloads);
    }

    public function retrieve(UuidInterface $aggregate_id): Game
    {
        $payloads = $this->event_repository->get($aggregate_id);

        $events = [];

        foreach ($payloads as $payload) {
            $events[] = $payload->event;
        }

        $game = new Game($aggregate_id);
        $game->loadEvents(...$events);

        return $game;
    }
}