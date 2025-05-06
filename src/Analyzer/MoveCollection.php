<?php

declare(strict_types=1);

namespace BattleSnake\Analyzer;

use Traversable;
use IteratorAggregate;
use ArrayIterator;

readonly class MoveCollection implements IteratorAggregate
{
    /** @var Move[] */
    private array $moves;

    /**
     * @param Move[] $moves
     */
    public function __construct(Move ...$moves)
    {
        $this->moves = $moves;
    }

    /**
     * @return Traversable<Move>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->moves);
    }

    /**
     * @return Move[]
     */
    public function toArray(): array
    {
        return $this->moves;
    }

    public function count(): int
    {
        return count($this->moves);
    }

    public function isEmpty(): bool
    {
        return empty($this->moves);
    }
}