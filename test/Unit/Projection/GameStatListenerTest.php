<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Projection;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\End;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\Event;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Projection\GameStat;
use BattleSnake\Projection\GameStatListener as SUT;
use BattleSnake\Root\Game;
use BattleSnake\Root\RootRepository;
use BattleSnake\Tests\Unit\ApplicationProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;

class GameStatListenerTest extends TestCase
{
    use ApplicationProvider;
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<RootRepository>
     */
    private ObjectProphecy $root_repository;
    private SUT $sut;

    protected function setUp(): void
    {
        $this->root_repository = $this->prophesize(RootRepository::class);

        $this->sut = new SUT(
            $this->getApplication()->entity_manager,
            $this->root_repository->reveal(),
        );
    }

    #[Test]
    #[DataProvider('provideNoopEvents')]
    public function invoke_ignores_other_events(Event $event): void
    {
        $aggregate_id = Uuid::uuid4();

        $this->sut->__invoke(new Payload(
            $aggregate_id,
            1,
            $event,
            new \DateTimeImmutable(),
        ));

        self::assertNull($this->getApplication()->entity_manager->find(
            GameStat::class,
            $aggregate_id,
        ));
    }

    #[Test]
    #[DataProvider('provideEndEvents')]
    public function invoke_creates_stat_projection(Event $event, bool $win, int $turn): void
    {
        $aggregate_id = Uuid::uuid4();

        $game = $this->prophesize(Game::class);
        $game->getTurn()->willReturn($turn);
        $this->root_repository->retrieve($aggregate_id)->willReturn($game);

        $this->sut->__invoke(new Payload(
            $aggregate_id,
            10,
            $event,
            new \DateTimeImmutable(),
        ));

        $stat = $this->getApplication()->entity_manager->find(
            GameStat::class,
            $aggregate_id,
        );

        self::assertNotNull($stat);
        self::assertInstanceOf(GameStat::class, $stat);
        self::assertSame($aggregate_id->toString(), $stat->getAggregateId()->toString());
        self::assertSame($win, $stat->isWin());
        self::assertSame($turn, $stat->getSurvived());
        self::assertSame(100, $stat->getLength()); // in the event payload
    }

    public static function provideNoopEvents(): \Generator
    {
        yield 'start' => [new Start(self::getJsonData('four-player-large-start'))];
        yield 'turn' => [new Turn(self::getJsonData('four-player-large-move1'))];
    }

    public static function provideEndEvents(): \Generator
    {
        yield 'loss' => [
            new End(self::getJsonData('four-player-large-end')),
            false,
            10,
        ];
        yield 'win' => [
            new End(self::getJsonData('four-player-large-alternate-end')),
            true,
            100,
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
