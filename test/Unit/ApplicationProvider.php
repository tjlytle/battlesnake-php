<?php

namespace BattleSnake\Tests\Unit;

use BattleSnake\ApplicationFactory;
use BattleSnake\Core\Application;

trait ApplicationProvider
{
    private Application $app;

    public function getApplication(): Application
    {
        return $this->app ??= $this->app = new ApplicationFactory()->make();
    }
}