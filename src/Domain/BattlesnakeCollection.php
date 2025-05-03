<?php

namespace BattleSnake\Domain;

use IteratorAggregate;
use Traversable;
use ArrayIterator;

/**
 * @implements IteratorAggregate<int, Battlesnake>
 */
readonly class BattlesnakeCollection implements IteratorAggregate
{
    /**
     * @var array<Battlesnake>
     */
    public array $battlesnakes; 

    public function __construct(Battlesnake ...$battlesnake)
    {
        $this->battlesnakes = $battlesnake;
    }

    /**
     * @return Traversable<int, Battlesnake>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->battlesnakes);
    }
}