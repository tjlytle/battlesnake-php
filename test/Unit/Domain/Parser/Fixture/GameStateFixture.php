<?php

namespace BattleSnake\Tests\Unit\Domain\Parser\Fixture;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\BattlesnakeCollection;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\Game;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Ruleset;
use BattleSnake\Domain\Setting\RulesetSettings;

interface GameStateFixture
{
    public function getState(): GameState;

    public function getGame(): Game;

    public function getTurn(): int;

    public function getBoard(): Board;

    public function getSelf(): Battlesnake;

    public function getSnakes(): BattleSnakeCollection;

    public function getRuleset(): Ruleset;

    public function getRulesetSettings(): RulesetSettings;

    public function getJson(): string;

    public function getLabel(): string;
}