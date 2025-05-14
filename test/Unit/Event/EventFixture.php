<?php

namespace BattleSnake\Tests\Unit\Event;

use BattleSnake\Domain\GameState;
use BattleSnake\Eventsource\Event;

readonly class EventFixture implements Event
{
    public function __construct(
        public string $string,
        public int $int,
        public float $float,
        public bool $bool,
        public array $array,
        public \DateTimeImmutable $dateTimeImmutable,
        public GameState $gameState,
    ) {
    }
}