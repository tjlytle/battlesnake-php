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

class OfficialExample extends JsonFixture
{
    #[\Override]
    public function getGame(): Game
    {
        return new Game(
            id: 'totally-unique-game-id',
            ruleset: $this->getRuleset(),
            map: 'standard',
            source: 'league',
            timeout: 500
        );
    }

    #[\Override] public function getBoard(): Board
    {
        return new Board(
            height: 11,
            width: 11,
            food: new CoordinateCollection(
                new Coordinate(5, 5),
                new Coordinate(9, 0),
                new Coordinate(2, 6),
            ),
            hazards: new CoordinateCollection(
                new Coordinate(3, 2),
            ),
            snakes: $this->getSnakes()
        );
    }

    #[\Override] public function getSelf(): Battlesnake
    {
        return $this->getSnake(
            id: 'snake-508e96ac-94ad-11ea-bb37',
            name: 'My Snake',
            head: new Coordinate(0, 0),
            latency: '111',
            shout: "why are we shouting??",
            customizations: new Customizations(
                color: '#FF0000',
                head: 'pixel',
                tail: 'pixel',
            ),
            health: 54,
            length: 3,
            body: new CoordinateCollection(
                new Coordinate(1, 0),
                new Coordinate(2, 0),
            ),
        );
    }

    #[\Override] public function getSnakes(): BattleSnakeCollection
    {
        return new BattleSnakeCollection(
            $this->getSelf(),
            $this->getSnake(
                id: 'snake-b67f4906-94ae-11ea-bb37',
                name: 'Another Snake',
                head: new Coordinate(5, 4),
                latency: "222",
                shout: "I'm not really sure...",
                customizations: new Customizations(
                    color: '#26CF04',
                    head: 'silly',
                    tail: 'curled',
                ),
                health: 16,
                length: 4,
                body: new CoordinateCollection(
                    new Coordinate(5, 3),
                    new Coordinate(6, 3),
                    new Coordinate(6, 2),
                )
            ),
        );
    }

    #[\Override] public function getRuleset(): Ruleset
    {
        return new Ruleset(
            name: 'standard',
            version: 'v1.1.15',
            settings: $this->getRulesetSettings()
        );
    }

    #[\Override] public function getRulesetSettings(): RulesetSettings
    {
        return new RulesetSettings(
            foodSpawnChance: 15,
            minimumFood: 1,
            hazardDamagePerTurn: 14,
        );
    }

    #[\Override] public function getJson(): string
    {
        return file_get_contents(__DIR__ .  '/../../../../requests/official-example.json');
    }

    #[\Override] public function getTurn(): int
    {
        return 14;
    }
}