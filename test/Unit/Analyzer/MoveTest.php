<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Analyzer;

use BattleSnake\Analyzer\Move as SUT;
use BattleSnake\Analyzer\Move\Classification;
use BattleSnake\Analyzer\Move\ClassificationCollection;
use BattleSnake\Domain\Direction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MoveTest extends TestCase
{
    #[Test]
    public function is_returns_expected_value(): void
    {
        $sut = new SUT(Direction::UP, new ClassificationCollection(Classification::SAFE, Classification::FOOD));

        self::assertTrue($sut->is(Classification::SAFE));
        self::assertTrue($sut->is(Classification::FOOD));
        self::assertFalse($sut->is(Classification::DANGER));
    }
}
