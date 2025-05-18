<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Analyzer;

use BattleSnake\Analyzer\SafeMove as SUT;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SafeMoveTest extends TestCase
{
    #[Test]
    #[DataProvider('provideGameStateExamples')]
    public function invoke_returns_valid_coord_collection(GameState $state, CoordinateCollection $expected): void
    {
        $sut = new SUT();
        $result = $sut($state);
        self::assertEqualsCanonicalizing($expected, $result);
    }

    public static function provideGameStateExamples(): \Generator
    {
        $example_requests = [
            'official-example' => new CoordinateCollection(
                new Coordinate(0, 1),
            ),
            'four-player-large-move1' => new CoordinateCollection(
                new Coordinate(0, 9),
                new Coordinate(1, 10),
                new Coordinate(2, 9),
                new Coordinate(1, 8),
            ),
            'four-player-large-move2' => new CoordinateCollection(
                new Coordinate(0, 10),
                new Coordinate(1, 11),
                new Coordinate(2, 10),
            ),
            'four-player-large-move3' => new CoordinateCollection(
                new Coordinate(0, 11),
                new Coordinate(1, 12),
                new Coordinate(2, 11),
            ),
            'four-player-large-move10' => new CoordinateCollection(
                new Coordinate(0, 18),
                new Coordinate(2, 18),
            ),
            'official-example-v1' => new CoordinateCollection(
                new Coordinate(4, 5),
                new Coordinate(4, 3),
            ),
            'official-example-v2' => new CoordinateCollection(
                new Coordinate(4, 2),
            ),
        ];

        $parser = GameStateParserFactory::make();
        foreach ($example_requests as $label => $expected) {
            $json = \file_get_contents(__DIR__ . "/../../requests/{$label}.json");
            if ($json === false) {
                throw new \RuntimeException("Failed to read JSON file for {$label}");
            }
            $state = $parser->parse(\json_decode($json, true));
            yield $label => [
                $state,
                $expected,
            ];
        }
    }
}
