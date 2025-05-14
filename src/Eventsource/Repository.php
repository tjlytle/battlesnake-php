<?php

namespace BattleSnake\Eventsource;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class Repository
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function persist(Payload ...$payload): void
    {
        $this->connection->beginTransaction();

        try {
            foreach ($payload as $item) {
                $this->connection->insert('game_events', [
                    'aggregate_id' => $item->uuid->toString(),
                    'version' => $item->version,
                    'date' => $item->timestamp->format('Y-m-d H:i:s'),
                    'event' => serialize($item->event)
                ]);
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function get(mixed $aggregateId): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM game_events WHERE aggregate_id = :aggregate_id ORDER BY version ASC');
        $stmt->bindValue(':aggregate_id', $aggregateId instanceof \Ramsey\Uuid\UuidInterface ? $aggregateId->toString() : $aggregateId);
        $result = $stmt->executeQuery();

        $payloads = [];
        while ($row = $result->fetchAssociative()) {
            $payloads[] = new Payload(
                Uuid::fromString($row['aggregate_id']),
                $row['version'],
                unserialize($row['event']),
                new \DateTimeImmutable($row['date'])
            );
        }

        return $payloads;
    }
}
