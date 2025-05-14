<?php

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
        var_dump('before');
        $this->getApplication()->connection->beginTransaction();
    }

    #[After]
    public function tearDownTransaction(): void
    {
        var_dump('after');
        $this->getApplication()->connection->rollBack();
    }
}
