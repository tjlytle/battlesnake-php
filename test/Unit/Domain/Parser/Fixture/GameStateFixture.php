<?php

namespace BattleSnake\Tests\Unit\Domain\Parser\Fixture;

use BattleSnake\Domain\GameState;

interface GameStateFixture
{
    public function getState(): GameState;
    public function getJson(): string;
    public function getLabel(): string;
}