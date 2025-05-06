<?php

namespace BattleSnake\Analyzer;

use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Coordinate;

class MoveClassifier
{
    public function __invoke(GameState $state): MoveCollection
    {
        $you = $state->you;
        $head = $you->head;
        $board = $state->board;
        
        $moves = [];
        
        // Analyze each possible move direction
        foreach ([Direction::UP, Direction::DOWN, Direction::LEFT, Direction::RIGHT] as $direction) {
            $position = $this->getAdjacentPosition($head, $direction);
            $classifications = [];
            
            // Check for food
            if ($this->isFood($position, $board)) {
                $classifications[] = Classification::FOOD;
            }
            
            // Check for hazards
            if ($this->isHazard($position, $board)) {
                $classifications[] = Classification::HAZARD;
            }
            
            // Check for potential snake collisions
            if ($this->isDangerous($position, $board, $you)) {
                $classifications[] = Classification::DANGER;
            }
            
            // If no classifications were assigned, it's safe
            if (empty($classifications)) {
                $classifications[] = Classification::SAFE;
            }

            // Any end condition can't also be another condition, so overwrite the list
            if ($this->isOutOfBounds($position, $board) || $this->isSnake($position, $board)) {
                $classifications = [Classification::END];
            }

            $moves[] = new Move($direction, new ClassificationCollection(...$classifications));
        }
        
        return new MoveCollection(...$moves);
    }
    
    /**
     * Calculate the position after moving in a given direction
     */
    private function getAdjacentPosition(Coordinate $position, Direction $direction): Coordinate
    {
        $x = $position->x;
        $y = $position->y;
        
        return match($direction) {
            Direction::UP => new Coordinate($x, $y + 1),
            Direction::DOWN => new Coordinate($x, $y - 1),
            Direction::LEFT => new Coordinate($x - 1, $y),
            Direction::RIGHT => new Coordinate($x + 1, $y),
        };
    }
    
    /**
     * Check if a position is outside the board boundaries
     */
    private function isOutOfBounds(Coordinate $position, $board): bool
    {
        return $position->x < 0 
            || $position->y < 0 
            || $position->x >= $board->width 
            || $position->y >= $board->height;
    }
    
    /**
     * Check if a position contains any part of a snake
     */
    private function isSnake(Coordinate $position, $board): bool
    {
        foreach ($board->snakes as $snake) {
            foreach ($snake->body as $segment) {
                if ($segment->x === $position->x && $segment->y === $position->y) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if a position contains food
     */
    private function isFood(Coordinate $position, $board): bool
    {
        foreach ($board->food as $food) {
            if ($food->x === $position->x && $food->y === $position->y) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a position contains a hazard
     */
    private function isHazard(Coordinate $position, $board): bool
    {
        foreach ($board->hazards as $hazard) {
            if ($hazard->x === $position->x && $hazard->y === $position->y) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a position is dangerous (potential collision with another snake)
     */
    private function isDangerous(Coordinate $position, $board, $you): bool
    {
        foreach ($board->snakes as $snake) {
            // Skip our own snake
            if ($snake->id === $you->id) {
                continue;
            }
            
            $head = $snake->head;
            
            // Check if another snake's head can move to this position
            // (Simple implementation - in a real game you'd consider snake length for ties)
            $possibleMoves = [
                new Coordinate($head->x, $head->y + 1),
                new Coordinate($head->x, $head->y - 1),
                new Coordinate($head->x - 1, $head->y),
                new Coordinate($head->x + 1, $head->y)
            ];
            
            foreach ($possibleMoves as $move) {
                if ($move->x === $position->x && $move->y === $position->y) {
                    return true;
                }
            }
        }
        
        return false;
    }
}