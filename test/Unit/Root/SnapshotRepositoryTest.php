<?php

namespace BattleSnake\Tests\Unit\Root;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\End;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\EventRepository;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Root\Game;
use BattleSnake\Root\RootRepository;
use BattleSnake\Root\SnapshotRepository as SUT;
use BattleSnake\Tests\Unit\ApplicationProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;

class SnapshotRepositoryTest extends TestCase
{
    use ApplicationProvider;
    use ProphecyTrait;

    /** @var ObjectProphecy<EventRepository> */
    private ObjectProphecy $event_repository;

    /** @var ObjectProphecy<RootRepository> */
    private ObjectProphecy $root_repository;
    protected function setUp(): void
    {
        $this->event_repository = $this->prophesize(EventRepository::class);
        $this->root_repository = $this->prophesize(RootRepository::class);

        $this->sut = new SUT(
            $this->event_repository->reveal(),
            $this->root_repository->reveal(),
            $this->getApplication()->connection,
        );
    }

    #[Test]
    public function snapshot_serializes_root_with_version_and_id(): void
    {
        $aggregate_id = Uuid::uuid4();
        $game = new Game($aggregate_id);
        $game->loadEvents(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new Turn(self::getJsonData('four-player-large-move3')),
            new Turn(self::getJsonData('four-player-large-move4')),
            new Turn(self::getJsonData('four-player-large-move5')),
            new Turn(self::getJsonData('four-player-large-move6')),
            new Turn(self::getJsonData('four-player-large-move7')),
        );

        $this->sut->snapshot($game);

        $connection = $this->getApplication()->connection;
        $stmt = $connection->prepare('SELECT * FROM game_snapshot WHERE aggregate_id = :aggregate_id');
        $stmt->bindValue(':aggregate_id', $aggregate_id, );
        $rows = $stmt->executeQuery()->fetchAllAssociative();

        self::assertCount(1, $rows);
        self::assertEquals($aggregate_id->toString(), $rows[0]['aggregate_id']);
        self::assertEquals($game->getVersion(), $rows[0]['version']);
        $serialized = $rows[0]['serialized'];

        self::assertEquals($game, unserialize($serialized));
    }

    #[Test]
    public function retrieveFromSnapshot_gets_all_events_without_snapshot(): void
    {
        $aggregate_id = Uuid::uuid4();

        $game = new Game($aggregate_id);
        $this->root_repository->retrieve($aggregate_id)->willReturn($game);

        self::assertSame($game, $this->sut->retrieveFromSnapshot($aggregate_id));
    }

    #[Test]
    public function retrieveFromSnapshot_uses_snapshot_and_only_gets_missing_events(): void
    {
        $aggregate_id = Uuid::uuid4();
        $game = new Game($aggregate_id);
        $game->loadEvents(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new Turn(self::getJsonData('four-player-large-move3')),
            new Turn(self::getJsonData('four-player-large-move4')),
            new Turn(self::getJsonData('four-player-large-move5')),
            new Turn(self::getJsonData('four-player-large-move6')),
            new Turn(self::getJsonData('four-player-large-move7')),
        );

        $this->sut->snapshot($game);

        $this->event_repository->get($aggregate_id, $game->getVersion())
            ->willReturn([
                new Payload($aggregate_id, $game->getVersion() + 1, new Turn(self::getJsonData('four-player-large-move8')), new \DateTimeImmutable()),
                new Payload($aggregate_id, $game->getVersion() + 2, new Turn(self::getJsonData('four-player-large-move9')), new \DateTimeImmutable()),
                new Payload($aggregate_id, $game->getVersion() + 3, new Turn(self::getJsonData('four-player-large-move10')), new \DateTimeImmutable()),
                new Payload($aggregate_id, $game->getVersion() + 4, new End(self::getJsonData('four-player-large-end')), new \DateTimeImmutable()),
            ]);


        $future_game = $this->sut->retrieveFromSnapshot($aggregate_id);

        self::assertTrue($future_game->isStarted());
        self::assertTrue($future_game->isFinished());
        self::assertSame(12 , $future_game->getVersion());
        self::assertSame(9, $future_game->getTurn());

        $this->sut->retrieveFromSnapshot($aggregate_id);
    }

    private static function getJsonData(string $string): GameState
    {
        $parser = GameStateParserFactory::make();
        $json = file_get_contents(__DIR__ . '/../../requests/' . $string . '.json');
        if ($json === false) {
            throw new \RuntimeException('Failed to read JSON file');
        }
        $data = json_decode($json, true);
        if ($data === null) {
            throw new \RuntimeException('Failed to decode JSON data');
        }
        return $parser->parse($data);
    }
}
