<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\GameState;

interface GameStateParserInterface
{
    /**
     * Parse JSON data into a GameState object
     *
     * @param array<string, mixed> $data
     */
    public function parse(array $data): GameState;
}
