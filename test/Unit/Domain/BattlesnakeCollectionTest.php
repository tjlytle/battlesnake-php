<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Domain;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\BattlesnakeCollection as SUT;
use BattleSnake\Domain\CoordinateCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BattlesnakeCollectionTest extends TestCase
{
    #[Test]
    public function construct_creates_traversable_collection(): void
    {
        $snake1 = new Battlesnake('1', 'Snake 1', 100, new CoordinateCollection());
        $snake2 = new Battlesnake('2', 'Snake 2', 100, new CoordinateCollection());

        $collection = new SUT($snake1, $snake2);
        $this->assertCount(2, $collection);

        foreach ($collection as $index => $snake) {
            $this->assertInstanceOf(Battlesnake::class, $snake);
            $this->assertSame(match ($index) {
                0 => $snake1,
                1 => $snake2,
                default => throw new \Exception('Unexpected index'),
            }, $snake);
        }
    }
}
