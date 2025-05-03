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

abstract class FourPlayerLarge implements GameStateFixture
{
    protected function getSelf(
        int $health = 100,
        int $length = 3,
        string $latency = '',
        string $shout = '',
        Coordinate $head = new Coordinate(1, 9),
        array $body = []
    ): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_cC8Gyx4GX8xcTD6wMyHMFjDF',
            name: 'Test PHP',
            head: $head,
            latency: $latency,
            shout: $shout,
            customizations: [
                'color' => '#ff0000',
                'head' => 'default',
                'tail' => 'default',
            ],
            health: $health,
            length: $length,
            body: $body,
        );
    }

    protected function getSnake(
        string $id,
        string $name,
        Coordinate $head,
        string $latency = '',
        string $shout = '',
        array $customizations = [],
        int $health = 100,
        int $length = 3,
        array $body = [],
    ): Battlesnake
    {
        if (empty($body)) {
            $body = [
                $head,
                $head,
            ];
        }

        return new Battlesnake(
            id: $id,
            name: $name,
            health: $health,
            body: [
                $head,
                ...$body,
            ],
            latency: $latency,
            head: $head,
            length: $length,
            shout: $shout,
            customizations: $customizations,
        );
    }

    protected function getHungry(
        int $health = 100,
        int $length = 3,
        string $latency = '',
        string $shout = '',
        Coordinate $head = new Coordinate(9, 17),
        array $body = []
    ): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_vvdjWCFQmxhd9KQ67mv468xV',
            name: 'Hungry Bot',
            head: $head,
            latency: $latency,
            shout: $shout,
            customizations: [
                'color' => '#00cc00',
                'head' => 'alligator',
                'tail' => 'alligator',
            ],
            health: $health,
            length: $length,
            body: $body,
        );
    }

    protected function getScared(
        int $health = 100,
        int $length = 3,
        string $latency = '',
        string $shout = '',
        Coordinate $head = new Coordinate(9, 1),
        array $body = []
    ): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_qRm6pdxvhbpPPj7wfSffxD6T',
            name: 'Scared Bot',
            head: $head,
            latency: $latency,
            shout: $shout,
            customizations: [
                'color' => '#000000',
                'head' => 'bendr',
                'tail' => 'curled',
            ],
            health: $health,
            length: $length,
            body: $body,
        );
    }

    public function getLoopy(
        int $health = 100,
        int $length = 3,
        string $latency = '',
        string $shout = '',
        Coordinate $head = new Coordinate(17, 9),
        array $body = []
    ): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_DcdCRQGcGHFCd6FD88gkGxkd',
            name: 'Loopy Bot',
            head: $head,
            latency: $latency,
            shout: $shout,
            customizations: [
                'color' => '#800080',
                'head' => 'caffeine',
                'tail' => 'iguana',
            ],
            health: $health,
            length: $length,
            body: $body,
        );
    }

    protected function getBoard(Battlesnake ...$snakes): Board
    {
        return new Board(
            height: 19,
            width: 19,
            food: [
                new Coordinate(0, 8),
                new Coordinate(8, 18),
                new Coordinate(10, 0),
                new Coordinate(18, 8),
                new Coordinate(9, 9),
            ],
            hazards: [],
            snakes: $snakes
        );
    }

    protected function getRulesetSettings(): RulesetSettings
    {
        return new RulesetSettings(
            foodSpawnChance: 20,
            minimumFood: 1,
            hazardDamagePerTurn: 14,
            royale: new RoyaleSettings(
                shrinkEveryNTurns: 25
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
            id: '662a6d46-ccb2-44dc-a527-437f1e69d10f',
            ruleset: $this->getRuleset(),
            map: 'royale',
            source: 'custom',
            timeout: 500
        );
    }

    public function getState(): GameState
    {
        return new GameState(
            game: $this->getGame(),
            turn: $this->getTurn(),
            board: $this->getBoard($this->getSelf(), $this->getHungry(), $this->getScared(), $this->getLoopy()),
            you: $this->getSelf(),
        );
    }

    protected function getTurn(): int
    {
        return 0;
    }

    public function getJson(): string
    {
        return static::JSON;
    }

    public function getLabel(): string
    {
        return static::class;
    }
}