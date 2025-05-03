<?php

namespace BattleSnake\Tests\Unit\Domain\Parser\Fixture;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\BattlesnakeCollection;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\Customizations;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RoyaleSettings;
use BattleSnake\Domain\Setting\RulesetSettings;
use BattleSnake\Domain\Setting\SquadSettings;

class FourPlayerRoyalMove5 extends JsonFixture
{
    #[\Override]
    public function getGame(): Game
    {
        return new Game(
            id: '662a6d46-ccb2-44dc-a527-437f1e69d10f',
            ruleset: $this->getRuleset(),
            map: 'royale',
            source: 'custom',
            timeout: 500
        );
    }

    #[\Override] public function getBoard(): Board
    {
        return new Board(
            height: 19,
            width: 19,
            food: new CoordinateCollection(
                new Coordinate(0, 8),
                new Coordinate(10, 0),
                new Coordinate(18, 8),
                new Coordinate(9, 9),
                new Coordinate(9, 12),
            ),
            hazards: new CoordinateCollection(),
            snakes: $this->getSnakes()
        );
    }

    #[\Override] public function getSelf(): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_cC8Gyx4GX8xcTD6wMyHMFjDF',
            name: 'Test PHP',
            head: new Coordinate(1, 13),
            latency: '60',
            shout: "Moving up!",
            customizations: new Customizations(
                color: '#ff0000',
                head: 'default',
                tail: 'default',
            ),
            health: 96,
            length: 3,
            body: new CoordinateCollection(
                new Coordinate(1, 12),
                new Coordinate(1, 11),
            ),
        );
    }

    #[\Override] public function getSnakes(): BattleSnakeCollection
    {
        return new BattleSnakeCollection(
            $this->getSelf(),
            $this->getSnake(
                id: 'gs_vvdjWCFQmxhd9KQ67mv468xV',
                name: 'Hungry Bot',
                head: new Coordinate(8, 16),
                latency: 1,
                customizations: new Customizations(
                    color: '#00cc00',
                    head: 'alligator',
                    tail: 'alligator',
                ),
                health: 98,
                length: 4,
                body: new CoordinateCollection(
                    new Coordinate(8, 17),
                    new Coordinate(8, 18),
                    new Coordinate(9, 18),
                )
            ),
            $this->getSnake(
                id: 'gs_qRm6pdxvhbpPPj7wfSffxD6T',
                name: 'Scared Bot',
                head: new Coordinate(8, 0),
                latency: "1",
                customizations: new Customizations(
                    color: '#000000',
                    head: 'bendr',
                    tail: 'curled',
                ),
                health: 96,
                length: 3,
                body: new CoordinateCollection(
                    new Coordinate(8, 1),
                    new Coordinate(8, 2),
                )
            ),
            $this->getSnake(
                id: 'gs_DcdCRQGcGHFCd6FD88gkGxkd',
                name: 'Loopy Bot',
                head: new Coordinate(17, 9),
                latency: "1",
                customizations: new Customizations(
                    color: '#800080',
                    head: 'caffeine',
                    tail: 'iguana',
                ),
                health: 96,
                length: 3,
                body: new CoordinateCollection(
                    new Coordinate(17, 8),
                    new Coordinate(16, 8),
                ),
            )

        );
    }

    #[\Override] public function getRuleset(): Ruleset
    {
        return new Ruleset(
            name: 'standard',
            version: 'v1.2.3',
            settings: $this->getRulesetSettings()
        );
    }

    #[\Override] public function getRulesetSettings(): RulesetSettings
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

    #[\Override] public function getJson(): string
    {
        return file_get_contents(__DIR__ .  '/../../../../requests/four-player-large-move5.json');
    }

    #[\Override] public function getTurn(): int
    {
        return 4;
    }
}