<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Domain\Parser;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

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
