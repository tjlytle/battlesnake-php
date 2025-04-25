<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RoyaleSettings;
use BattleSnake\Domain\Setting\RulesetSettings;
use BattleSnake\Domain\Setting\SquadSettings;

final class GameStateParser implements GameStateParserInterface
{
    /**
     * Parse JSON data into a GameState object
     *
     * @param array<string, mixed> $data
     */
    public function parse(array $data): GameState
    {
        $game = $this->parseGame($data['game'] ?? []);
        $turn = (int)($data['turn'] ?? 0);
        $board = $this->parseBoard($data['board'] ?? []);
        $you = $this->parseSnake($data['you'] ?? []);

        return new GameState(
            $game,
            $turn,
            $board,
            $you
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function parseGame(array $data): Game
    {
        $rulesetData = $data['ruleset'] ?? [];
        $ruleset = new Ruleset(
            $rulesetData['name'] ?? '',
            $rulesetData['version'] ?? '',
            $this->parseRulesetSettings($rulesetData['settings'] ?? [])
        );

        return new Game(
            $data['id'] ?? '',
            $ruleset,
            $data['map'] ?? '',
            $data['source'] ?? '',
            isset($data['timeout']) ? (int)$data['timeout'] : null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function parseBoard(array $data): Board
    {
        $height = (int)($data['height'] ?? 0);
        $width = (int)($data['width'] ?? 0);
        
        $food = array_map(
            fn(array $food) => new Coordinate((int)($food['x'] ?? 0), (int)($food['y'] ?? 0)),
            $data['food'] ?? []
        );
        
        $hazards = array_map(
            fn(array $hazard) => new Coordinate((int)($hazard['x'] ?? 0), (int)($hazard['y'] ?? 0)),
            $data['hazards'] ?? []
        );
        
        $snakes = array_map(
            fn(array $snake) => $this->parseSnake($snake),
            $data['snakes'] ?? []
        );

        return new Board(
            $height,
            $width,
            $food,
            $hazards,
            $snakes
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function parseSnake(array $data): Battlesnake
    {
        $body = array_map(
            fn(array $segment) => new Coordinate((int)($segment['x'] ?? 0), (int)($segment['y'] ?? 0)),
            $data['body'] ?? []
        );

        $head = null;
        if (isset($data['head']) && is_array($data['head'])) {
            $head = new Coordinate(
                (int)($data['head']['x'] ?? 0),
                (int)($data['head']['y'] ?? 0)
            );
        }

        return new Battlesnake(
            $data['id'] ?? '',
            $data['name'] ?? '',
            (int)($data['health'] ?? 0),
            $body,
            $data['latency'] ?? '',
            $head,
            (int)($data['length'] ?? 0),
            $data['shout'] ?? '',
            $data['squad'] ?? '',
            $data['customizations'] ?? []
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function parseRulesetSettings(array $data): RulesetSettings
    {
        $royaleSettings = null;
        if (isset($data['royale']) && is_array($data['royale'])) {
            $royaleSettings = new RoyaleSettings(
                (int)($data['royale']['shrinkEveryNTurns'] ?? 0)
            );
        }

        $squadSettings = null;
        if (isset($data['squad']) && is_array($data['squad'])) {
            $squadSettings = new SquadSettings(
                (bool)($data['squad']['allowBodyCollisions'] ?? false),
                (bool)($data['squad']['sharedElimination'] ?? false),
                (bool)($data['squad']['sharedHealth'] ?? false),
                (bool)($data['squad']['sharedLength'] ?? false)
            );
        }

        return new RulesetSettings(
            (int)($data['foodSpawnChance'] ?? 0),
            (int)($data['minimumFood'] ?? 0),
            (int)($data['hazardDamagePerTurn'] ?? 0),
            $royaleSettings,
            $squadSettings
        );
    }
}
