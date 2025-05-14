<?php

namespace BattleSnake\Event;

use BattleSnake\Domain\GameState;
use BattleSnake\Eventsource\Event;

readonly class Start implements Event
{
    public function __construct(
        public GameState $game,
    )
    {
    }
}