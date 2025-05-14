<?php

namespace BattleSnake\Eventsource;

use Doctrine\DBAL\Connection;

class Repository
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function persist(Payload ...$payload): void
    {

    }
}