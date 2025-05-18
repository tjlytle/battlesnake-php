<?php

declare(strict_types=1);

namespace BattleSnake\Event;

use BattleSnake\Domain\GameState;
use BattleSnake\Eventsource\Event;

readonly class End implements Event
{
    public function __construct(
        public GameState $game,
    ) {
    }
}
