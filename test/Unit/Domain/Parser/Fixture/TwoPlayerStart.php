<?php

namespace BattleSnake\Tests\Unit\Domain\Parser\Fixture;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RoyaleSettings;
use BattleSnake\Domain\Setting\RulesetSettings;
use BattleSnake\Domain\Setting\SquadSettings;

class TwoPlayerStart implements GameStateFixture
{
    public const string JSON = <<<JSON
    {
      "game": {
        "id": "36e38494-b7cb-4974-b122-f1916cd888c4",
        "ruleset": {
          "name": "standard",
          "version": "v1.2.3",
          "settings": {
            "foodSpawnChance": 15,
            "minimumFood": 1,
            "hazardDamagePerTurn": 0,
            "hazardMap": "",
            "hazardMapAuthor": "",
            "royale": {
              "shrinkEveryNTurns": 0
            },
            "squad": {
              "allowBodyCollisions": false,
              "sharedElimination": false,
              "sharedHealth": false,
              "sharedLength": false
            }
          }
        },
        "map": "standard",
        "timeout": 500,
        "source": "custom"
      },
      "turn": 0,
      "board": {
        "height": 11,
        "width": 11,
        "snakes": [
          {
            "id": "gs_ggpXgpCGFrgvSKdcGkWrx7Jc",
            "name": "Test PHP",
            "latency": "",
            "health": 100,
            "body": [
              {
                "x": 1,
                "y": 1
              },
              {
                "x": 1,
                "y": 1
              },
              {
                "x": 1,
                "y": 1
              }
            ],
            "head": {
              "x": 1,
              "y": 1
            },
            "length": 3,
            "shout": "",
            "squad": "",
            "customizations": {
              "color": "#ff0000",
              "head": "default",
              "tail": "default"
            }
          },
          {
            "id": "gs_b94PTPV7mkcQCTbgw3pVGXMT",
            "name": "Hungry Bot",
            "latency": "",
            "health": 100,
            "body": [
              {
                "x": 9,
                "y": 9
              },
              {
                "x": 9,
                "y": 9
              },
              {
                "x": 9,
                "y": 9
              }
            ],
            "head": {
              "x": 9,
              "y": 9
            },
            "length": 3,
            "shout": "",
            "squad": "",
            "customizations": {
              "color": "#00cc00",
              "head": "alligator",
              "tail": "alligator"
            }
          }
        ],
        "food": [
          {
            "x": 2,
            "y": 0
          },
          {
            "x": 10,
            "y": 8
          },
          {
            "x": 5,
            "y": 5
          }
        ],
        "hazards": []
      },
      "you": {
        "id": "gs_ggpXgpCGFrgvSKdcGkWrx7Jc",
        "name": "Test PHP",
        "latency": "",
        "health": 100,
        "body": [
          {
            "x": 1,
            "y": 1
          },
          {
            "x": 1,
            "y": 1
          },
          {
            "x": 1,
            "y": 1
          }
        ],
        "head": {
          "x": 1,
          "y": 1
        },
        "length": 3,
        "shout": "",
        "squad": "",
        "customizations": {
          "color": "#ff0000",
          "head": "default",
          "tail": "default"
        }
      }
    }
    JSON;

    protected function getSelf(): Battlesnake
    {
        return new Battlesnake(
            id: 'gs_ggpXgpCGFrgvSKdcGkWrx7Jc',
            name: 'Test PHP',
            health: 100,
            body: [
                new Coordinate(1, 1),
                new Coordinate(1, 1),
                new Coordinate(1, 1),
            ],
            head: new Coordinate(1, 1),
            length: 3,
            customizations: [
                'color' => '#ff0000',
                'head' => 'default',
                'tail' => 'default'
            ]
        );
    }

    protected function getOther(): Battlesnake
    {
        return new Battlesnake(
            id: 'gs_b94PTPV7mkcQCTbgw3pVGXMT',
            name: 'Hungry Bot',
            health: 100,
            body: [
                new Coordinate(9, 9),
                new Coordinate(9, 9),
                new Coordinate(9, 9),
            ],
            head: new Coordinate(9, 9),
            length: 3,
            customizations: [
                'color' => '#00cc00',
                'head' => 'alligator',
                'tail' => 'alligator'
            ]
        );
    }

    protected function getBoard(Battlesnake ...$snakes): Board
    {
        return new Board(
            height: 11,
            width: 11,
            food: [
                new Coordinate(2, 0),
                new Coordinate(10, 8),
                new Coordinate(5, 5),
            ],
            hazards: [],
            snakes: $snakes
        );
    }

    protected function getRulesetSettings(): RulesetSettings
    {
        return new RulesetSettings(
            foodSpawnChance: 15,
            minimumFood: 1,
            hazardDamagePerTurn: 0,
            royale: new RoyaleSettings(
                shrinkEveryNTurns: 0
            ),
            squad: new SquadSettings(
                allowBodyCollisions: false,
                sharedElimination: false,
                sharedHealth: false,
                sharedLength: false
            )
        );
    }
    protected function getRuleset(): Ruleset
    {
        return new Ruleset(
            name: 'standard',
            version: 'v1.2.3',
            settings: $this->getRulesetSettings()
        );
    }

    protected function getGame(): Game
    {
        return new Game(
            id: '36e38494-b7cb-4974-b122-f1916cd888c4',
            ruleset: $this->getRuleset(),
            map: 'standard',
            source: 'custom',
            timeout: 500
        );
    }

    public function getState(): GameState
    {
        return new GameState(
            game: $this->getGame(),
            turn: 0,
            board: $this->getBoard($this->getSelf(), $this->getOther()),
            you: $this->getSelf(),
        );
    }

    public function getJson(): string
    {
        return self::JSON;
    }

    public function getLabel(): string
    {
        return 'Simple Start';
    }
}