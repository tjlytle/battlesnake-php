<?php

namespace BattleSnake\Tests\Unit\Analyzer;

use BattleSnake\Analyzer\Move;
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
                new Move(Direction::UP, new Move\ClassificationCollection(Move\Classification::END)),
                new Move(Direction::LEFT, new Move\ClassificationCollection(Move\Classification::END)),
                new Move(Direction::DOWN, new Move\ClassificationCollection(Move\Classification::HAZARD)),
                new Move(Direction::RIGHT, new Move\ClassificationCollection(Move\Classification::FOOD, Move\Classification::DANGER)),
            )
        ];
    }
}
