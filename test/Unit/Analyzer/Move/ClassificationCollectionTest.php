<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Analyzer\Move;

use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassificationCollectionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_classifications(): void
    {
        $classification1 = Classification::FOOD;
        $classification2 = Classification::SAFE;

        $collection = new ClassificationCollection($classification1, $classification2);

        self::assertCount(2, $collection);
        self::assertSame([$classification1, $classification2], $collection->toArray());
        self::assertFalse($collection->isEmpty());
    }

    #[Test]
    public function it_can_be_created_empty(): void
    {
        $collection = new ClassificationCollection();

        self::assertCount(0, $collection);
        self::assertSame([], $collection->toArray());
        self::assertTrue($collection->isEmpty());
    }

    #[Test]
    public function it_is_iterable(): void
    {
        $classification1 = Classification::DANGER;
        $classification2 = Classification::HAZARD;

        $collection = new ClassificationCollection($classification1, $classification2);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame([$classification1, $classification2], $items);
    }
}
