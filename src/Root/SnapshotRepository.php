<?php

declare(strict_types=1);

namespace BattleSnake\Root;

use BattleSnake\Eventsource\EventRepository;
use BattleSnake\Eventsource\Payload;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;

class SnapshotRepository implements Repository
{
    public function __construct(
        private readonly EventRepository $event_repository,
        private readonly RootRepository $root_repository,
        private readonly Connection $connection,
    ) {
    }

    #[\Override]
    public function retrieve(UuidInterface $aggregate_id): Game
    {
        return $this->root_repository->retrieve($aggregate_id);
    }

    public function retrieveFromSnapshot(UuidInterface $aggregate_id): Game
    {
        $stmt = $this->connection->prepare('SELECT version, serialized FROM game_snapshot WHERE aggregate_id = :aggregate_id');
        $stmt->bindValue('aggregate_id', $aggregate_id->toString());
        $snapshot = $stmt->executeQuery()->fetchAssociative();

        if ($snapshot === false) {
            return $this->root_repository->retrieve($aggregate_id);
        }

        $game = \unserialize($snapshot['serialized']);

        $events = $this->event_repository->get($aggregate_id, $snapshot['version']);
        $game->loadEvents(...\array_map(fn(Payload $payload) => $payload->event, $events));

        return $game;
    }

    #[\Override]
    public function persist(AggregateRoot $root): void
    {
        $this->root_repository->persist($root);
    }

    public function snapshot(AggregateRoot $root): void
    {
        $stmt = $this->connection->prepare('INSERT INTO game_snapshot (aggregate_id, version, serialized) VALUES (:aggregate_id, :version, :serialized) ON DUPLICATE KEY UPDATE version = :version, serialized = :serialized');
        $stmt->bindValue('aggregate_id', $root->getAggregateRootId()->toString());
        $stmt->bindValue('version', $root->getVersion());
        $stmt->bindValue('serialized', \serialize($root));

        $stmt->executeStatement();
    }
}
