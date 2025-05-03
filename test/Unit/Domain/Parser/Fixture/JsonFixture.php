<?php

namespace BattleSnake\Tests\Unit\Domain\Parser\Fixture;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\Customizations;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RoyaleSettings;
use BattleSnake\Domain\Setting\RulesetSettings;
use BattleSnake\Domain\Setting\SquadSettings;

abstract class JsonFixture implements GameStateFixture
{
    /**
     * Simplify the creation of a Battlesnake object with some default values
     * for testing.
     *
     * Because the head is included in the body, and because the start position
     * has all three coordinates the same, we can just pass a head and the rest
     * will default.
     *
     * Health and length are also defaulted to the start values.
     */
    protected function getSnake(
        string $id,
        string $name,
        Coordinate $head,
        string $latency = '',
        string $shout = '',
        Customizations $customizations = new Customizations(),
        int $health = 100,
        int $length = 3,
        CoordinateCollection $body = new CoordinateCollection(),
    ): Battlesnake
    {
        if (empty($body->coordinates)) {
            $body = new CoordinateCollection($head, $head);
        }

        return new Battlesnake(
            id: $id,
            name: $name,
            health: $health,
            body: new CoordinateCollection(
                $head,
                ...$body
            ),
            latency: $latency,
            head: $head,
            length: $length,
            shout: $shout,
            customizations: $customizations,
        );
    }

    public function getState(): GameState
    {
        return new GameState(
            game: $this->getGame(),
            turn: $this->getTurn(),
            board: $this->getBoard(),
            you: $this->getSelf(),
        );
    }

    public function getTurn(): int
    {
        return 0;
    }

    public function getLabel(): string
    {
        return static::class;
    }
}