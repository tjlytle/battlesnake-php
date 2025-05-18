<?php

declare(strict_types=1);

namespace BattleSnake\Eventsource;

use BattleSnake\Event\End;
use BattleSnake\Event\Nudge;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Tests\Unit\Eventsource\EventFixture;
use Crell\Serde\Attributes\StaticTypeMap;

readonly class EventWrapper
{
    public function __construct(
        #[StaticTypeMap(
            key: 'type',
            map: [
            'test' => EventFixture::class,
            'start' => Start::class,
            'end' => End::class,
            'turn' => Turn::class,
            'nudge' => Nudge::class,
            ],
        )]
        public Event $event,
    ) {
    }
}
