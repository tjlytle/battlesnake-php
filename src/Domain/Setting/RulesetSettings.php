<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Setting;

final readonly class RulesetSettings
{
    public function __construct(
        public int $foodSpawnChance,
        public int $minimumFood,
        public int $hazardDamagePerTurn,
        public ?RoyaleSettings $royale = null,
        public ?SquadSettings $squad = null,
    ) {
    }
}
