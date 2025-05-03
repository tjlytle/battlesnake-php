<?php

declare(strict_types=1);

namespace BattleSnake\Domain\Parser;

use BattleSnake\Domain\Battlesnake;
use BattleSnake\Domain\Coordinate;
use BattleSnake\Domain\CoordinateCollection;
use BattleSnake\Domain\Customizations;

final class BattlesnakeParser
{
    /**
     * @param array<string, mixed> $data
     */
    public function parse(array $data): Battlesnake
    {
        $bodyCoordinates = array_map(
            fn(array $segment) => new Coordinate((int)($segment['x'] ?? 0), (int)($segment['y'] ?? 0)),
            $data['body'] ?? []
        );
        
        $body = new CoordinateCollection(...$bodyCoordinates);

        $head = null;
        if (isset($data['head']) && is_array($data['head'])) {
            $head = new Coordinate(
                (int)($data['head']['x'] ?? 0),
                (int)($data['head']['y'] ?? 0)
            );
        }

        $customizationsData = $data['customizations'] ?? [];
        $customizations = new Customizations(
            $customizationsData['color'] ?? null,
            $customizationsData['head'] ?? null,
            $customizationsData['tail'] ?? null
        );
        
        return new Battlesnake(
            $data['id'] ?? '',
            $data['name'] ?? '',
            (int)($data['health'] ?? 0),
            $body,
            $data['latency'] ?? '',
            $head,
            (int)($data['length'] ?? 0),
            $data['shout'] ?? '',
            $data['squad'] ?? '',
            $customizations
        );
    }
}
