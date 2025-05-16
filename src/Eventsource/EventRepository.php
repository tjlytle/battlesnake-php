<?php

namespace BattleSnake\Eventsource;

use Crell\Serde\Serde;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class EventRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Serde $serde,
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
                    'event' => $this->serde->serialize(new EventWrapper($item->event), 'json')
                ]);
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            // Check if this is a duplicate key/integrity constraint violation
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'Duplicate entry') !== false || 
                strpos($errorMessage, 'UNIQUE constraint failed') !== false ||
                strpos($errorMessage, 'integrity constraint violation') !== false) {
                throw new VersionCollision('Version collision detected', 0, $e);
            }

            throw $e;
        }
    }

    /**
     * @return Payload[]
     */
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
                $this->serde->deserialize($row['event'], 'json', EventWrapper::class)->event,
                new \DateTimeImmutable($row['date'])
            );
        }

        return $payloads;
    }

    /**
     * @return \Generator<Payload>
     */
    public function getAll(): \Generator
    {
        $stmt = $this->connection->prepare('SELECT * FROM game_events ORDER BY date ASC');
        $result = $stmt->executeQuery();

        while ($row = $result->fetchAssociative()) {
            yield new Payload(
                Uuid::fromString($row['aggregate_id']),
                $row['version'],
                $this->serde->deserialize($row['event'], 'json', EventWrapper::class)->event,
                new \DateTimeImmutable($row['date'])
            );
        }
    }
}
