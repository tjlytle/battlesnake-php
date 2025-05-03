<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\BattlesnakeCollection;
use BattleSnake\Domain\Board;
use BattleSnake\Domain\GameState;

final class GameStateParser implements GameStateParserInterface
{
    public function __construct(
        private GameParser $gameParser,
        private BoardParser $boardParser,
        private BattlesnakeParser $snakeParser
    ) {
    }

    /**
     * Parse JSON data into a GameState object
     *
     * @param array<string, mixed> $data
     */
    public function parse(array $data): GameState
    {
        $game = $this->gameParser->parse($data['game'] ?? []);
        $turn = (int)($data['turn'] ?? 0);
        $board = $this->boardParser->parse($data['board'] ?? []);
        $you = $this->snakeParser->parse($data['you'] ?? []);

        return new GameState(
            $game,
            $turn,
            $board,
            $you
        );
    }
}
