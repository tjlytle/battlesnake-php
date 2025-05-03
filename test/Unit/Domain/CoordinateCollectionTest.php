<?php

namespace BattleSnake\Tests\Unit\Domain;

use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection as SUT;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CoordinateCollectionTest extends TestCase
{
    #[Test]
    public function construct_creates_traversable_collection(): void
    {
        $coordinate1 = new Coordinate(1, 2);
        $coordinate2 = new Coordinate(3, 4);

        $collection = new SUT($coordinate1, $coordinate2);
        $this->assertCount(2, $collection);

        foreach ($collection as $index => $coordinate) {
            $this->assertInstanceOf(Coordinate::class, $coordinate);
            $this->assertSame(match($index) {
                0 => $coordinate1,
                1 => $coordinate2,
                default => throw new \Exception('Unexpected index'),
            }, $coordinate);
        }
    }
}
