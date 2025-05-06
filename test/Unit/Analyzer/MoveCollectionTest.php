<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Analyzer;

use BattleSnake\Analyzer\Move;
use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use BattleSnake\Analyzer\MoveCollection;
use BattleSnake\Domain\Direction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoveCollectionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_moves(): void
    {
        $move1 = new Move(Direction::UP, new ClassificationCollection(Classification::SAFE));
        $move2 = new Move(Direction::DOWN, new ClassificationCollection(Classification::FOOD));

        $collection = new MoveCollection($move1, $move2);

        self::assertCount(2, $collection);
        self::assertSame([$move1, $move2], $collection->toArray());
        self::assertFalse($collection->isEmpty());
    }

    #[Test]
    public function it_can_be_created_empty(): void
    {
        $collection = new MoveCollection();

        self::assertCount(0, $collection);
        self::assertSame([], $collection->toArray());
        self::assertTrue($collection->isEmpty());
    }

    #[Test]
    public function it_is_iterable(): void
    {
        $move1 = new Move(Direction::LEFT, new ClassificationCollection(Classification::DANGER));
        $move2 = new Move(Direction::RIGHT, new ClassificationCollection(Classification::HAZARD));

        $collection = new MoveCollection($move1, $move2);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame([$move1, $move2], $items);
    }
}
