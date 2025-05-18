<?php

declare(strict_types=1);

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

class FourPlayerRoyalEnd extends JsonFixture
{
    #[\Override]
    public function getGame(): Game
    {
        return new Game(
            id: '662a6d46-ccb2-44dc-a527-437f1e69d10f',
            ruleset: $this->getRuleset(),
            map: 'royale',
            source: 'custom',
            timeout: 500,
        );
    }

    #[\Override] public function getBoard(): Board
    {
        return new Board(
            height: 19,
            width: 19,
            food: new CoordinateCollection(
                new Coordinate(18, 8),
                new Coordinate(10, 17),
                new Coordinate(14, 17),
                new Coordinate(15, 3),
                new Coordinate(12, 1),
                new Coordinate(11, 1),
                new Coordinate(2, 6),
            ),
            hazards: new CoordinateCollection(
                new Coordinate(0, 0),
                new Coordinate(0, 1),
                new Coordinate(0, 2),
                new Coordinate(0, 3),
                new Coordinate(0, 4),
                new Coordinate(0, 5),
                new Coordinate(0, 6),
                new Coordinate(0, 7),
                new Coordinate(0, 8),
                new Coordinate(0, 9),
                new Coordinate(0, 10),
                new Coordinate(0, 11),
                new Coordinate(0, 12),
                new Coordinate(0, 13),
                new Coordinate(0, 14),
                new Coordinate(0, 15),
                new Coordinate(0, 16),
                new Coordinate(0, 17),
                new Coordinate(0, 18),
                new Coordinate(1, 17),
                new Coordinate(1, 18),
                new Coordinate(2, 17),
                new Coordinate(2, 18),
                new Coordinate(3, 17),
                new Coordinate(3, 18),
                new Coordinate(4, 17),
                new Coordinate(4, 18),
                new Coordinate(5, 17),
                new Coordinate(5, 18),
                new Coordinate(6, 17),
                new Coordinate(6, 18),
                new Coordinate(7, 17),
                new Coordinate(7, 18),
                new Coordinate(8, 17),
                new Coordinate(8, 18),
                new Coordinate(9, 17),
                new Coordinate(9, 18),
                new Coordinate(10, 17),
                new Coordinate(10, 18),
                new Coordinate(11, 17),
                new Coordinate(11, 18),
                new Coordinate(12, 17),
                new Coordinate(12, 18),
                new Coordinate(13, 17),
                new Coordinate(13, 18),
                new Coordinate(14, 17),
                new Coordinate(14, 18),
                new Coordinate(15, 17),
                new Coordinate(15, 18),
                new Coordinate(16, 17),
                new Coordinate(16, 18),
                new Coordinate(17, 17),
                new Coordinate(17, 18),
                new Coordinate(18, 0),
                new Coordinate(18, 1),
                new Coordinate(18, 2),
                new Coordinate(18, 3),
                new Coordinate(18, 4),
                new Coordinate(18, 5),
                new Coordinate(18, 6),
                new Coordinate(18, 7),
                new Coordinate(18, 8),
                new Coordinate(18, 9),
                new Coordinate(18, 10),
                new Coordinate(18, 11),
                new Coordinate(18, 12),
                new Coordinate(18, 13),
                new Coordinate(18, 14),
                new Coordinate(18, 15),
                new Coordinate(18, 16),
                new Coordinate(18, 17),
                new Coordinate(18, 18),
            ),
            snakes: $this->getSnakes(),
        );
    }

    #[\Override] public function getSelf(): Battlesnake
    {
        return $this->getSnake(
            id: 'gs_cC8Gyx4GX8xcTD6wMyHMFjDF',
            name: 'Test PHP',
            head: new Coordinate(1, 19),
            latency: '60',
            shout: "Moving up!",
            customizations: new Customizations(
                color: '#ff0000',
                head: 'default',
                tail: 'default',
            ),
            health: 90,
            length: 3,
            body: new CoordinateCollection(
                new Coordinate(1, 18),
                new Coordinate(1, 17),
            ),
        );
    }

    #[\Override] public function getSnakes(): BattleSnakeCollection
    {
        return new BattleSnakeCollection(
            // No self, because we're dead.
            $this->getSnake(
                id: 'gs_vvdjWCFQmxhd9KQ67mv468xV',
                name: 'Hungry Bot',
                head: new Coordinate(7, 15),
                latency: 1,
                customizations: new Customizations(
                    color: '#00cc00',
                    head: 'alligator',
                    tail: 'alligator',
                ),
                health: 100,
                length: 21,
                body: new CoordinateCollection(
                    new Coordinate(6, 15),
                    new Coordinate(5, 15),
                    new Coordinate(5, 14),
                    new Coordinate(5, 13),
                    new Coordinate(4, 13),
                    new Coordinate(4, 12),
                    new Coordinate(4, 11),
                    new Coordinate(5, 11),
                    new Coordinate(6, 11),
                    new Coordinate(7, 11),
                    new Coordinate(7, 12),
                    new Coordinate(8, 12),
                    new Coordinate(9, 12),
                    new Coordinate(10, 12),
                    new Coordinate(11, 12),
                    new Coordinate(12, 12),
                    new Coordinate(12, 11),
                    new Coordinate(11, 11),
                    new Coordinate(10, 11),
                    new Coordinate(10, 11),
                ),
            ),
        );
    }

    #[\Override] public function getRuleset(): Ruleset
    {
        return new Ruleset(
            name: 'standard',
            version: 'v1.2.3',
            settings: $this->getRulesetSettings(),
        );
    }

    #[\Override] public function getRulesetSettings(): RulesetSettings
    {
        return new RulesetSettings(
            foodSpawnChance: 20,
            minimumFood: 1,
            hazardDamagePerTurn: 14,
            royale: new RoyaleSettings(
                shrinkEveryNTurns: 25,
            ),
            squad: new SquadSettings(
                allowBodyCollisions: false,
                sharedElimination: false,
                sharedHealth: false,
                sharedLength: false,
            ),
        );
    }

    #[\Override] public function getJson(): string
    {
        return \file_get_contents(__DIR__ . '/../../../../requests/four-player-large-end.json');
    }

    #[\Override] public function getTurn(): int
    {
        return 100;
    }
}
