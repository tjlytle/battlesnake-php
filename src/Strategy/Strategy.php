<?php

namespace BattleSnake\Strategy;

use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;

interface Strategy
{
    public function __invoke(GameState $game_state): Direction;
}