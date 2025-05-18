<?php

declare(strict_types=1);

namespace BattleSnake\Command;

use BattleSnake\Domain\Direction;
use BattleSnake\Event\Nudge as NudgeEvent;
use BattleSnake\Root\Game;

class Nudge implements Command
{
    public function __construct(
        public readonly Direction $direction,
    ) {
    }

    #[\Override]
    public function process(Game $game): array
    {
        return [new NudgeEvent($this->direction)];
    }
}
