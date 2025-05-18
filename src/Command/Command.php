<?php

declare(strict_types=1);

namespace BattleSnake\Command;

use BattleSnake\Eventsource\Event;
use BattleSnake\Root\Game;

interface Command
{
    /**
     * @return Event[]
     */
    public function process(Game $game): array;
}
