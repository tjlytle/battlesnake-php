<?php

namespace BattleSnake\Domain;

enum Direction: string
{
    case UP = 'up';
    case DOWN = 'down';
    case LEFT = 'left';
    case RIGHT = 'right';
}
