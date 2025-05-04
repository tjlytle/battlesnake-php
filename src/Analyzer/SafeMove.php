<?php

namespace BattleSnake\Analyzer;

use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\GameState;

class SafeMove
{
    public function __invoke(GameState $state): CoordinateCollection
    {
        $head = $state->you->head;
        
        if ($head === null) {
            return new CoordinateCollection();
        }
        
        $possibleMoves = [
            new Coordinate($head->x, $head->y - 1), // up
            new Coordinate($head->x, $head->y + 1), // down
            new Coordinate($head->x - 1, $head->y), // left
            new Coordinate($head->x + 1, $head->y), // right
        ];
        
        // Filter out moves that would hit walls
        $validMoves = array_filter($possibleMoves, function (Coordinate $coordinate) use ($state) {
            return $this->isWithinBounds($coordinate, $state);
        });
        
        // Filter out moves that would collide with snake bodies
        $validMoves = array_filter($validMoves, function (Coordinate $coordinate) use ($state) {
            return $this->isNotCollidingWithSnakes($coordinate, $state);
        });
        
        // Filter out moves that would collide with hazards (if needed)
        // This could be added if hazards are dangerous in your game implementation
        
        return new CoordinateCollection(...$validMoves);
    }
    
    private function isWithinBounds(Coordinate $coordinate, GameState $state): bool
    {
        return 
            $coordinate->x >= 0 && 
            $coordinate->x < $state->board->width && 
            $coordinate->y >= 0 && 
            $coordinate->y < $state->board->height;
    }
    
    private function isNotCollidingWithSnakes(Coordinate $coordinate, GameState $state): bool
    {
        foreach ($state->board->snakes as $snake) {
            // Check for collision with any snake body part (except tail which will move)
            $bodyParts = count($snake->body->coordinates);
            
            // Check all body segments except the tail (which will move)
            for ($i = 0; $i < $bodyParts - 1; $i++) {
                $segment = $snake->body->coordinates[$i];
                if ($segment->x === $coordinate->x && $segment->y === $coordinate->y) {
                    return false;
                }
            }
        }
        
        return true;
    }
}