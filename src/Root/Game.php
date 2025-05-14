<?php

namespace BattleSnake\Root;

use BattleSnake\Event\End;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\Event;
use Ramsey\Uuid\UuidInterface;

class Game
{
    private bool $is_finished = false;
    private bool $is_started = false;
    private int $turn = 0;

    public function __construct(
        public readonly UuidInterface $id
    ) {
    }

    public function loadEvents(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    private function apply(Event $event): void
    {
        if ($event instanceof Start) {
            $this->is_finished = false;
            $this->is_started = false;
        } elseif ($event instanceof Turn) {
            $this->is_started = true;
            $this->turn = $event->game->turn;
        } elseif ($event instanceof End) {
            $this->is_finished = true;
        }
    }

    public function isFinished(): bool
    {
        return $this->is_finished;
    }

    public function isStarted(): bool
    {
        return $this->is_started;
    }

    public function getTurn(): int
    {
        return $this->turn;
    }
}
