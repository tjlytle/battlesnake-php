<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\Game;
use BattleSnake\Domain\Ruleset;

final class GameParser
{
    public function __construct(
        private RulesetSettingsParser $rulesetSettingsParser
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function parse(array $data): Game
    {
        $rulesetData = $data['ruleset'] ?? [];
        $ruleset = new Ruleset(
            $rulesetData['name'] ?? '',
            $rulesetData['version'] ?? '',
            $this->rulesetSettingsParser->parse($rulesetData['settings'] ?? [])
        );

        return new Game(
            $data['id'] ?? '',
            $ruleset,
            $data['map'] ?? '',
            $data['source'] ?? '',
            isset($data['timeout']) ? (int)$data['timeout'] : null
        );
    }
}
