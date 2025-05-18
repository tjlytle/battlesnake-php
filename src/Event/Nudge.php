<?php

declare(strict_types=1);

namespace BattleSnake\Event;

use BattleSnake\Domain\Direction;
use BattleSnake\Eventsource\Event;

readonly class Nudge implements Event
{
    public function __construct(
        public Direction $direction,
    ) {
    }
}
