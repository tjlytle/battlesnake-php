<?php

declare(strict_types=1);

namespace BattleSnake\Analyzer\Move;

use Traversable;
use IteratorAggregate;
use ArrayIterator;

readonly class ClassificationCollection implements IteratorAggregate
{
    /** @var Classification[] */
    private array $classifications;

    /**
     * @param Classification[] $classifications
     */
    public function __construct(Classification ...$classifications)
    {
        $this->classifications = $classifications;
    }

    /**
     * @return Traversable<Classification>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->classifications);
    }

    /**
     * @return Classification[]
     */
    public function toArray(): array
    {
        return $this->classifications;
    }

    public function count(): int
    {
        return count($this->classifications);
    }

    public function isEmpty(): bool
    {
        return empty($this->classifications);
    }
}