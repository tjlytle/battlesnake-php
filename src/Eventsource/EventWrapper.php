<?php

namespace BattleSnake\Eventsource;

use BattleSnake\Tests\Unit\Event\EventFixture;
use Crell\Serde\Attributes\StaticTypeMap;

readonly class EventWrapper
{
    public function __construct(
        #[StaticTypeMap(key: 'type', map: [
            'test' => EventFixture::class,
        ])]
        public Event $event,
    ){
    }
}