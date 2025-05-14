<?php

namespace BattleSnake\Domain;

use Crell\Serde\Attributes\SequenceField;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

/**
 * @implements IteratorAggregate<int, Coordinate>
 */
readonly class CoordinateCollection implements IteratorAggregate
{
    /**
     * @var array<Coordinate>
     */
    #[SequenceField(arrayType: Coordinate::class)]
    public array $coordinates;

    public function __construct(Coordinate ...$coordinate)
    {
        $this->coordinates = $coordinate;
    }

    /**
     * @return Traversable<int, Coordinate>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->coordinates);
    }
}