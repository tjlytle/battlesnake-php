<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Strategy;

use BattleSnake\Analyzer\Move;
use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use BattleSnake\Analyzer\MoveClassifier;
use BattleSnake\Analyzer\MoveCollection;
use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RulesetSettings;
use BattleSnake\Root\Game;
use BattleSnake\Root\Repository;
use BattleSnake\Strategy\Nudgeable as SUT;
use BattleSnake\Strategy\Strategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Argument\Token\TokenInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class NudgeableTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<MoveClassifier>
     */
    private ObjectProphecy $classifier;

    /**
     * @var ObjectProphecy<Repository>
     */
    private ObjectProphecy $repository;

    /**
     * @var ObjectProphecy<Strategy>
     */
    private ObjectProphecy $strategy;

    private SUT $sut;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->strategy = $this->prophesize(Strategy::class);
        $this->classifier = $this->prophesize(MoveClassifier::class);

        $this->sut = new SUT(
            $this->repository->reveal(),
            $this->strategy->reveal(),
            $this->classifier->reveal(),
        );
    }

    #[Test]
    public function without_nudge_use_fallback_strategy(): void
    {
        $game = $this->prophesize(Game::class);
        $game->getLastNudge()->willReturn(null);

        $id = Uuid::uuid4();
        $game_state = $this->getGameState($id);
        $this->repository->retrieve(self::UuidArgument($id))->willReturn($game->reveal());
        $this->strategy->__invoke($game_state)->willReturn(Direction::UP);

        $result = ($this->sut)($game_state);

        self::assertSame(Direction::UP, $result);
    }

    #[Test]
    public function ignore_bad_nudge(): void
    {
        $game = $this->prophesize(Game::class);
        $game->getLastNudge()->willReturn(Direction::DOWN);

        $id = Uuid::uuid4();
        $game_state = $this->getGameState($id);
        $this->repository->retrieve(self::UuidArgument($id))->willReturn($game->reveal());
        $this->strategy->__invoke($game_state)->willReturn(Direction::UP);

        $this->classifier->__invoke($game_state)->willReturn(
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::END)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::SAFE)),
            ),
        );

        $result = ($this->sut)($game_state);

        self::assertSame(Direction::UP, $result);
    }

    #[Test]
    public function go_in_possible_nudge_direction(): void
    {
        $game = $this->prophesize(Game::class);
        $game->getLastNudge()->willReturn(Direction::DOWN);

        $id = Uuid::uuid4();
        $game_state = $this->getGameState($id);
        $this->repository->retrieve(self::UuidArgument($id))->willReturn($game->reveal());
        $this->strategy->__invoke($game_state)->willReturn(Direction::UP);

        $this->classifier->__invoke($game_state)->willReturn(
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::SAFE)),
            ),
        );

        $result = ($this->sut)($game_state);

        self::assertSame(Direction::DOWN, $result);
    }

    private function getGameState(UuidInterface $id): GameState
    {
        return new GameState(
            game: new \BattleSnake\Domain\Game(
                id: $id->toString(),
                ruleset: new Ruleset(
                    name: 'standard',
                    version: 'v1.0.0',
                    settings: new RulesetSettings(
                        foodSpawnChance: 0,
                        minimumFood: 0,
                        hazardDamagePerTurn: 0,
                    ),
                ),
            ),
            turn: 1,
            board: new \BattleSnake\Domain\Board(
                height: 11,
                width: 11,
            ),
            you: new Battlesnake(
                id: 'snake-id',
                name: 'snake-name',
                health: 100,
            ),
        );
    }

    private static function UuidArgument(UuidInterface $id): TokenInterface
    {
        return Argument::that(
            function (UuidInterface $uuid) use ($id) {
                return $uuid->toString() === $id->toString();
            },
        );
    }
}
