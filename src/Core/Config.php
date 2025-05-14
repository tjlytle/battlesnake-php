<?php

namespace BattleSnake\Core;

readonly class Config
{
    public function __construct(
        public string $dsn,
    )
    {
    }
}