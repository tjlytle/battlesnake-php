<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Root;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\End;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\Event;
use BattleSnake\Root\Game as SUT;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GameTest extends TestCase
{
    #[Test]
    #[DataProvider('provideEventsAndAssertion')]
    public function can_rebuild_from_past_events(callable $assertion, Event ...$events): void
    {
        $uuid = Uuid::uuid4();
        $game = new SUT($uuid);
        $game->loadEvents(...$events);

        $assertion($game);

        self::assertSame(\count($events), $game->getVersion());
    }




    #[Test]
    public function can_add_new_events(): void
    {
        $uuid = Uuid::uuid4();
        $game = new SUT($uuid);

        $game->loadEvents(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new Turn(self::getJsonData('four-player-large-move3')),
            new Turn(self::getJsonData('four-player-large-move4')),
        );

        self::assertSame(3, $game->getTurn());

        $game->addEvent(
            new Turn(self::getJsonData('four-player-large-move6')),
            new Turn(self::getJsonData('four-player-large-move7')),
            new Turn(self::getJsonData('four-player-large-move8')),
            new Turn(self::getJsonData('four-player-large-move9')),
            new Turn(self::getJsonData('four-player-large-move10')),
            new End(self::getJsonData('four-player-large-end')),
        );

        self::assertSame(9, $game->getTurn());
        self::assertTrue($game->isFinished());
        self::assertSame(11, $game->getVersion());
    }

    #[Test]
    public function can_get_events_not_persisted(): void
    {
        $uuid = Uuid::uuid4();
        $game = new SUT($uuid);

        $game->loadEvents(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new Turn(self::getJsonData('four-player-large-move3')),
            new Turn(self::getJsonData('four-player-large-move4')),
        );

        self::assertSame(3, $game->getTurn());

        $events = [
            new Turn(self::getJsonData('four-player-large-move6')),
            new Turn(self::getJsonData('four-player-large-move7')),
            new Turn(self::getJsonData('four-player-large-move8')),
            new Turn(self::getJsonData('four-player-large-move9')),
            new Turn(self::getJsonData('four-player-large-move10')),
            new End(self::getJsonData('four-player-large-end')),
        ];

        $game->addEvent(...$events);

        self::assertSame($events, $game->drainEventBuffer());

        self::assertSame([], $game->drainEventBuffer());
    }

    public static function provideEventsAndAssertion(): \Generator
    {
        $events = [
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new Turn(self::getJsonData('four-player-large-move3')),
            new Turn(self::getJsonData('four-player-large-move4')),
            new Turn(self::getJsonData('four-player-large-move5')),
            new Turn(self::getJsonData('four-player-large-move6')),
            new Turn(self::getJsonData('four-player-large-move7')),
            new Turn(self::getJsonData('four-player-large-move8')),
            new Turn(self::getJsonData('four-player-large-move9')),
            new Turn(self::getJsonData('four-player-large-move10')),
            new End(self::getJsonData('four-player-large-end')),
        ];

        yield [
            function (SUT $game) {
                TestCase::assertFalse($game->isFinished());
                TestCase::assertFalse($game->isStarted());
            },
            $events[0],
        ];

        yield [
            function (SUT $game) {
                TestCase::assertFalse($game->isFinished());
                TestCase::assertTrue($game->isStarted());
                TestCase::assertSame(0, $game->getTurn());
            },
            $events[0],
            $events[1],
        ];

        yield [
            function (SUT $game) {
                TestCase::assertFalse($game->isFinished());
                TestCase::assertTrue($game->isStarted());
                TestCase::assertSame(2, $game->getTurn());
            },
            $events[0],
            $events[1],
            $events[2],
            $events[3],
        ];

        yield [
            function (SUT $game) {
                TestCase::assertTrue($game->isFinished());
                TestCase::assertTrue($game->isStarted());
                TestCase::assertSame(9, $game->getTurn());
            },
            ...$events,
        ];
    }

    private static function getJsonData(string $string): GameState
    {
        $parser = GameStateParserFactory::make();
        $json = \file_get_contents(__DIR__ . '/../../requests/' . $string . '.json');
        if ($json === false) {
            throw new \RuntimeException('Failed to read JSON file');
        }
        $data = \json_decode($json, true);
        if ($data === null) {
            throw new \RuntimeException('Failed to decode JSON data');
        }
        return $parser->parse($data);
    }
}
