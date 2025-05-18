<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit;

use BattleSnake\ApplicationFactory;
use BattleSnake\Core\Application;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait ApplicationProvider
{
    private Application $app;

    public function getApplication(): Application
    {
        return $this->app ??= $this->app = new ApplicationFactory()->make();
    }

    #[Before]
    public function setUpTransaction(): void
    {
        $this->getApplication()->connection->beginTransaction();
    }

    #[After]
    public function tearDownTransaction(): void
    {
        $this->getApplication()->connection->rollBack();
    }
}
