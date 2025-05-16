<?php

namespace BattleSnake\Tests\Unit\Analyzer;

use BattleSnake\Analyzer\Move;
use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use BattleSnake\Analyzer\MoveClassifier as SUT;
use BattleSnake\Analyzer\MoveCollection;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Tests\SnekSpec\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MoveClassifierTest extends TestCase
{
    #[Test]
    #[DataProvider('provideClassifications')]
    public function invoke_classifies(string $state, MoveCollection $expected_moves): void
    {
        $state_parser = GameStateParserFactory::make();
        $parser = new Parser();
        $json = $parser->parse($state);
        $state = $state_parser->parse($json);

        $sut = new SUT();
        $moves = $sut($state);
        $this->assertEquals($expected_moves, $moves);
    }

    public static function provideClassifications(): \Generator
    {
        $state = <<<EOD
            -----------
            -----------
            -----0-----
            -----A0----
            -----bC----
            -----bd----
            -----Bd----
            ------D----
            -----------
            -----------
            -----------
            EOD;

        yield [
            $state,
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::SAFE, Classification::FOOD)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::END)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::FOOD, Classification::DANGER)),
            )
        ];

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

        yield [
            $state,
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::END)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::END)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::HAZARD)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::FOOD, Classification::DANGER)),
            )
        ];

        $state = <<<EOD
            -----------
            -----------
            -----/-----
            -----A0----
            -----bC----
            -----bd----
            -----Bd----
            ------D----
            -----------
            -----------
            -----------
            EOD;

        yield [
            $state,
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::HAZARD)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::END)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::FOOD, Classification::DANGER)),
            )
        ];


        $state = <<<EOD
            -----------
            -----------
            -----------
            -----------
            -BbbbA-----
            -----------
            -----------
            -------CdD-
            -----------
            -----------
            -----------
            EOD;

        yield [
            $state,
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::END)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::SAFE)),
            )
        ];

        $state = <<<EOD
            -----------
            -----E-----
            -----------
            -----------
            ----A------
            -----------
            -----------
            ----C------
            -----------
            -----------
            -----------
            EOD;

        yield [
            $state,
            new MoveCollection(
                new Move(Direction::UP, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::LEFT, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::DOWN, new ClassificationCollection(Classification::SAFE)),
                new Move(Direction::RIGHT, new ClassificationCollection(Classification::SAFE)),
            )
        ];

    }
}
