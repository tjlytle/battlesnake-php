<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Root;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\EventRepository;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Root\Game;
use BattleSnake\Root\RootRepository as SUT;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;

class RootRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<EventDispatcherInterface>
     */
    private ObjectProphecy $dispatcher;

    /**
     * @var ObjectProphecy<EventRepository>
     */
    private ObjectProphecy $event_repository;
    private SUT $sut;

    protected function setUp(): void
    {
        $this->event_repository = $this->prophesize(EventRepository::class);

        $this->sut = new SUT(
            $this->event_repository->reveal()
        );
    }


    #[Test]
    public function persist_creates_payloads_and_passes_to_EventRepository(): void
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
        ];

        $aggregate_id = Uuid::uuid4();
        $game = new Game($aggregate_id);

        $game->loadEvents(
            $events[0],
            $events[1],
            $events[2],
            $events[3],
            $events[4],
        );

        $game->addEvent($events[5], $events[6], $events[7]);

        $this->event_repository->persist(Argument::cetera())
            ->will(function ($args) use ($events, $aggregate_id) {
                TestCase::assertCount(3, $args);

                $version = 5;

                foreach ($args as $payload) {
                    $version++;
                    TestCase::assertInstanceOf(Payload::class, $payload);
                    TestCase::assertSame($version, $payload->version);
                    TestCase::assertSame($events[$version - 1], $payload->event);
                    TestCase::assertSame($aggregate_id, $payload->uuid);
                }
            })->shouldBeCalled();

        $this->sut->persist($game);
    }

    #[Test]
    public function retrieve_loads_all_events_and_returns_aggregate_root()
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
        ];

        $aggregate_id = Uuid::uuid4();
        $payloads = [];
        $version = 0;

        foreach ($events as $event) {
            $payloads[] = new Payload(
                $aggregate_id,
                ++$version,
                $event,
                new \DateTimeImmutable(),
            );
        }

        $this->event_repository->get($aggregate_id)->willReturn($payloads);

        $game = $this->sut->retrieve($aggregate_id);

        TestCase::assertInstanceOf(Game::class, $game);
        self::assertSame(6, $game->getTurn());
        self::assertTrue($game->isStarted());
        self::assertFalse($game->isFinished());
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
