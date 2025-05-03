<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\Board;
use BattleSnake\Domain\BattlesnakeCollection;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;

final class BoardParser
{
    public function __construct(
        private BattlesnakeParser $snakeParser
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function parse(array $data): Board
    {
        $height = (int)($data['height'] ?? 0);
        $width = (int)($data['width'] ?? 0);
        
        $foodCoordinates = array_map(
            fn(array $food) => new Coordinate((int)($food['x'] ?? 0), (int)($food['y'] ?? 0)),
            $data['food'] ?? []
        );
        $food = new CoordinateCollection(...$foodCoordinates);
        
        $hazardCoordinates = array_map(
            fn(array $hazard) => new Coordinate((int)($hazard['x'] ?? 0), (int)($hazard['y'] ?? 0)),
            $data['hazards'] ?? []
        );
        $hazards = new CoordinateCollection(...$hazardCoordinates);
        
        $battlesnakes = array_map(
            fn(array $snake) => $this->snakeParser->parse($snake),
            $data['snakes'] ?? []
        );
        $snakes = new BattlesnakeCollection(...$battlesnakes);

        return new Board(
            $height,
            $width,
            $food,
            $hazards,
            $snakes
        );
    }
}
