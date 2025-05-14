<?php

namespace BattleSnake\Tests\Unit\Event;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParser;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Eventsource\Event;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Eventsource\Repository as SUT;
use BattleSnake\Tests\SnekSpec\Parser;
use BattleSnake\Tests\Unit\ApplicationProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class RepositoryTest extends TestCase
{
    use ApplicationProvider;

    private GameStateParser $state_parser;
    private Parser $test_parser;

    protected function setUp(): void
    {
        $this->state_parser = GameStateParserFactory::make();
        $this->test_parser = new Parser();

        $this->sut = new SUT();
    }

    #[Test]
    public function persist_saves_ordered_event(Event ...$events): void
    {
        // given a set of events
        $event1 = new EventFixture(
            'event1',
            1,
            1.0,
            true,
            ['array'],
            new \DateTimeImmutable(),
            $this->getGame(),
        );

        $event2 = new EventFixture(
            'event2',
            2,
            1.2,
            false,
            [1, 'red' , new stdClass()],
            new \DateTimeImmutable(),
            $this->getGame(),
        );

        $event3 = new EventFixture(
            'event3',
            3,
            3.4,
            false,
            [],
            new \DateTimeImmutable(),
            $this->getGame(),
        );

        // passed to persist with an aggregate id and a version
        $aggregate_id = Uuid::uuid7();
        $now = new \DateTimeImmutable();
        $this->sut->persist(
            new Payload($aggregate_id, 1,  $event1, $now),
            new Payload($aggregate_id, 2,  $event2, $now),
            new Payload($aggregate_id, 3,  $event3, $now),
        );

        // puts rows in database
        $app = $this->getApplication();
        $connection = $app->connection;

        $stmt = $connection->prepare('SELECT * FROM game_events WHERE aggregate_id = :aggregate_id');
        $stmt->bindValue(':aggregate_id', $aggregate_id);
        self::assertSame(3, $stmt->executeQuery()->rowCount());

        // and are returned when getting events
        $events = $this->sut->get($aggregate_id);
        self::assertCount(3, $events);
        self::assertEquals($event1, $events[0]->event);
        self::assertEquals($event2, $events[1]->event);
        self::assertEquals($event3, $events[2]->event);
    }

    #[Test]
    public function persist_rejects_ordered_collisions(): void
    {
        // given a set of events

        // passed to persist with an aggregate id and a version

        // throws an exception when the version is already used
    }

    private function getGame(): GameState
    {
        $state = <<<EOD
            -----------
            -----------
            -----------
            bbbB-------
            b----------
            A0Cdd------
            /---d------
            -----------
            -----------
            -----------
            -----------
            EOD;

        return $this->state_parser->parse(
            $this->test_parser->parse($state)
        );
    }

}
