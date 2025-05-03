<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Domain\Parser;

use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\BattlesnakeParser;
use BattleSnake\Domain\Parser\BoardParser;
use BattleSnake\Domain\Parser\GameParser;
use BattleSnake\Domain\Parser\GameStateParser;
use BattleSnake\Domain\Parser\RulesetSettingsParser;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerRoyalEnd;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\GameStateFixture;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerRoyalMove5;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerRoyalStart;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\OfficialExample;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GameStateParserTest extends TestCase
{
    /*
     * TODO: refactor to use the examples, but also setup the value objects
     *       directly and compare.
     */

    private GameStateParser $parser;

    protected function setUp(): void
    {
        $this->parser = new GameStateParser(
            new GameParser(new RulesetSettingsParser()),
            new BoardParser(new BattlesnakeParser()),
            new BattlesnakeParser()
        );
    }

    #[Test]
    #[DataProvider('provideJsonExamples')]
    public function parse_returns_expected_GameState(string $json, GameState $expectedGameState): void
    {
        $state = $this->parser->parse(json_decode($json, true));
        self::assertEquals($expectedGameState, $state);
    }

    public static function provideJsonExamples(): \Generator
    {
        $fixtures = [
            FourPlayerRoyalStart::class,
            FourPlayerRoyalMove5::class,
            FourPlayerRoyalEnd::class,
            OfficialExample::class,
        ];

        foreach ($fixtures as $fixture) {
            $fixture = new $fixture();
            \assert($fixture instanceof GameStateFixture);
            yield $fixture->getLabel() => [
                'json' => $fixture->getJson(),
                'expectedGameState' => $fixture->getState(),
            ];
        }
    }
}
