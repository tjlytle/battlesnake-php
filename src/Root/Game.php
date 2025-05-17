<?php

namespace BattleSnake\Root;

use BattleSnake\Command\Command;
use BattleSnake\Domain\Direction;
use BattleSnake\Event\End;
use BattleSnake\Event\Nudge;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Eventsource\Event;
use Ramsey\Uuid\UuidInterface;

class Game implements AggregateRoot
{
    private array $event_buffer = [];
    private Direction|null $last_nudge = null;
    private int $version = 0;

    private bool $is_finished = false;
    private bool $is_started = false;
    private int $turn = 0;

    public function __construct(
        public readonly UuidInterface $id
    ) {
    }

    public function getAggregateRootId(): UuidInterface
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function loadEvents(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    public function drainEventBuffer(): array
    {
        $events = $this->event_buffer;
        $this->event_buffer = [];
        return $events;
    }

    public function addEvent(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->bufferEvent($event);
        }
    }

    public function process(Command $command): void
    {
        // guard against invalid commands, right now no command is valid after
        // a game is finished
        if ($this->is_finished) {
            throw new InvalidState('Game is finished');
        }

        $this->addEvent(...$command->process($this));
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
        } elseif ($event instanceof Nudge) {
            $this->last_nudge = $event->direction;
        }

        $this->version++;
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

    public function getLastNudge(): ?Direction
    {
        return $this->last_nudge;
    }

    private function bufferEvent(Event $event): void
    {
        $this->event_buffer[] = $event;
        $this->apply($event);
    }
}
