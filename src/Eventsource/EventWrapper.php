<?php

namespace BattleSnake\Eventsource;

use BattleSnake\Event\End;
use BattleSnake\Event\Move;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Tests\Unit\Event\EventFixture;
use Crell\Serde\Attributes\StaticTypeMap;

readonly class EventWrapper
{
    public function __construct(
        #[StaticTypeMap(key: 'type', map: [
            'test' => EventFixture::class,
            'start' => Start::class,
            'end' => End::class,
            'turn' => Turn::class,
        ])]
        public Event $event,
    ){
    }
}