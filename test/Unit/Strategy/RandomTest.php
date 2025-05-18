<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Strategy;

use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Strategy\Random as SUT;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RandomTest extends TestCase
{
    //TODO: use this as an example of a test that mocks too much
    #[Test]
    #[DataProvider('provideGameStateExamples')]
    public function invoke_returns_random_safe_direction(GameState $state, Direction ...$safe_direction): void
    {
        $sut = new SUT();
        $result = $sut($state);
        self::assertContains($result, $safe_direction);
    }

    public static function provideGameStateExamples(): \Generator
    {
        $example_requests = [
            'official-example' => [
                Direction::UP,
            ],
            'four-player-large-move1' => [
                Direction::UP,
                Direction::DOWN,
                Direction::RIGHT,
                Direction::LEFT,
            ],
            'four-player-large-move2' => [
                Direction::UP,
                Direction::RIGHT,
                Direction::LEFT,
            ],
            'four-player-large-move3' => [
                Direction::UP,
                Direction::RIGHT,
                Direction::LEFT,
            ],
            'four-player-large-move10' => [
                Direction::RIGHT,
                Direction::LEFT,
            ],
            'official-example-v1' => [
                Direction::UP,
                Direction::DOWN,
            ],
            'official-example-v2' => [
                Direction::DOWN,
            ],
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
                ...$expected,
            ];
        }
    }
}
