<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Domain\Parser;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParser;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\AnotherTest;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeEnd;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove1;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove10;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove2;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove3;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove4;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove5;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove6;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove7;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove8;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeMove9;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\FourPlayerLargeStart;
use BattleSnake\Tests\Unit\Domain\Parser\Fixture\TwoPlayerStart;
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
    private string $fixturesDir;
    
    protected function setUp(): void
    {
        $this->parser = new GameStateParser();
        $this->fixturesDir = __DIR__ . '/Fixtures';
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
            TwoPlayerStart::class,
            FourPlayerLargeStart::class,
            FourPlayerLargeMove1::class,
            FourPlayerLargeMove2::class,
            FourPlayerLargeMove3::class,
            FourPlayerLargeMove4::class,
            FourPlayerLargeMove5::class,
            FourPlayerLargeMove6::class,
            FourPlayerLargeMove7::class,
            FourPlayerLargeMove8::class,
            FourPlayerLargeMove9::class,
            FourPlayerLargeMove10::class,
            FourPlayerLargeEnd::class,
        ];

        foreach ($fixtures as $fixture) {
            $fixture = new $fixture();
            yield $fixture->getLabel() => [
                'json' => $fixture->getJson(),
                'expectedGameState' => $fixture->getState(),
            ];
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function exampleProvider(): array
    {
        $examples = [];

        // Include custom examples
        $examples['example-1'] = ['example-1'];
        $examples['example-2'] = ['example-2'];
        $examples['example-3'] = ['example-3'];
        $examples['example-4'] = ['example-4'];
        
        return $examples;
    }
    
    /**
     * @dataProvider exampleProvider
     */
    public function testCanParseGameStateFromJson(string $exampleName): void
    {
        // Get example data
        $exampleData = $this->loadExampleData($exampleName);
        
        // Parse the JSON data
        $gameState = $this->parser->parse($exampleData);
        
        // Assert common structure 
        $this->assertInstanceOf(GameState::class, $gameState);
        $this->assertInstanceOf(Game::class, $gameState->game);
        $this->assertInstanceOf(Board::class, $gameState->board);
        $this->assertInstanceOf(Battlesnake::class, $gameState->you);
        
        // Validate game ID matches
        $this->assertSame($exampleData['game']['id'], $gameState->game->id);
        
        // Validate turn matches
        $this->assertSame((int)$exampleData['turn'], $gameState->turn);
        
        // Validate board dimensions
        $this->assertSame((int)$exampleData['board']['height'], $gameState->board->height);
        $this->assertSame((int)$exampleData['board']['width'], $gameState->board->width);
        
        // Validate food
        $this->assertCount(count($exampleData['board']['food']), $gameState->board->food);
        foreach ($exampleData['board']['food'] as $index => $foodData) {
            $this->assertCoordinate((int)$foodData['x'], (int)$foodData['y'], $gameState->board->food[$index]);
        }
        
        // Validate hazards
        $hazardCount = count($exampleData['board']['hazards'] ?? []);
        $this->assertCount($hazardCount, $gameState->board->hazards);
        if ($hazardCount > 0) {
            foreach ($exampleData['board']['hazards'] as $index => $hazardData) {
                $this->assertCoordinate((int)$hazardData['x'], (int)$hazardData['y'], $gameState->board->hazards[$index]);
            }
        }
        
        // Validate snakes
        $this->assertCount(count($exampleData['board']['snakes']), $gameState->board->snakes);
        foreach ($exampleData['board']['snakes'] as $index => $snakeData) {
            $snake = $gameState->board->snakes[$index];
            $this->assertSame($snakeData['id'], $snake->id);
            $this->assertSame($snakeData['name'], $snake->name);
            $this->assertSame((int)$snakeData['health'], $snake->health);
            $this->assertCount(count($snakeData['body']), $snake->body);
            $this->assertCoordinate((int)$snakeData['head']['x'], (int)$snakeData['head']['y'], $snake->head);
        }
        
        // Validate you snake
        $this->assertSame($exampleData['you']['id'], $gameState->you->id);
        $this->assertSame($exampleData['you']['name'], $gameState->you->name);
        $this->assertSame((int)$exampleData['you']['health'], $gameState->you->health);
        
        // Basic validation that "you" snake exists in board snakes list
        $youFound = false;
        foreach ($gameState->board->snakes as $snake) {
            if ($snake->id === $gameState->you->id) {
                $youFound = true;
                break;
            }
        }
        $this->assertTrue($youFound, 'The "you" snake should exist in the board snakes list');
    }
    
    private function loadExampleData(string $exampleName): array
    {
        if ($exampleName === 'open-api') {
            // Load from open-api.yaml
            $yamlContent = file_get_contents(__DIR__ . '/../../../../open-api.yaml');
            $openApi = Yaml::parse($yamlContent);
            return $openApi['paths']['/move']['post']['requestBody']['content']['application/json']['examples']['example-move-request']['value'];
        }
        
        // Load from JSON fixture file
        $jsonContent = file_get_contents($this->fixturesDir . '/' . $exampleName . '.json');
        return json_decode($jsonContent, true);
    }
    
    private function assertCoordinate(int $expectedX, int $expectedY, Coordinate $coordinate): void
    {
        $this->assertSame($expectedX, $coordinate->x, "X coordinate mismatch");
        $this->assertSame($expectedY, $coordinate->y, "Y coordinate mismatch");
    }
}
