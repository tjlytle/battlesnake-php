<?php

namespace BattleSnake\Tests\SnekSpec;

class Parser
{
    public function parse(string $board): array
    {
        // coords start from the bottom left corner
        $lines = explode("\n", $board);
        $lines = array_reverse($lines);

        $height = count($lines);
        $width = strlen($lines[0]);

        $i = -1;

        $food = [];
        $hazards = [];
        $snake_segments = [];

        foreach ($lines as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                $i++;
                if ($x === $width) {
                    if ($char !== "\n") {
                        throw new \UnexpectedValueException("Mock is missing expected newline at $i");
                    }
                    continue;
                }

                $coord = ['x' => $x, 'y' => $y];

                if ($char === '-') {
                    continue;
                }

                if ($char === '0') {
                    $food[] = $coord;
                    continue;
                }

                if ($char === '/') {
                    $hazards[] = $coord;
                    continue;
                }

                if (!preg_match('#[A-Ya-y]#', $char)) {
                    throw new \UnexpectedValueException("Mock has unexpected character $char at $i");
                }

                if (!isset($snake_segments[$char])) {
                    $snake_segments[$char] = [];
                }

                $snake_segments[$char][] = $coord;
            }
        }

        // find heads as having only one coordinate and not having a matching body
        $heads = array_filter($snake_segments, function(array $coords, string $key) use ($snake_segments): bool  {
            return count($coords) === 1 && !isset($snake_segments[strtolower($key)]);
        }, ARRAY_FILTER_USE_BOTH);

        // sort heads by order to handle two segment snakes
        ksort($heads);
        $heads = array_filter($heads, fn(string $key): bool => !isset($heads[chr(ord($key) - 1)]), ARRAY_FILTER_USE_KEY);
        var_dump($heads);

        $snakes = [];
        foreach ($heads as $head => $head_coords) {
            $snake = $this->traverseSnakeBody((string) $head, $snake_segments);

            $snakes[] = [
                'id' => strtolower($head),
                'name' => strtolower($head),
                'head' => $head_coords[0],
                'body' => $snake,
                'length' => count($snake),
                'health' => 90,
                'shout' => 'boo!',
                'squad' => '',
                'latency' => '111',
            ];

        }

        return [
            'board' => [
                'height' => $height,
                'width' => $width,
                'food' => $food,
                'hazards' => $hazards,
                'snakes' => $snakes,
            ],
        ];
    }

    private function traverseSnakeBody(string $head, array $snake_segments)
    {
        $coords = [];
        $coords[] = $snake_segments[$head][0];

        $tail = chr(ord($head) + 1);

        if (!isset($snake_segments[$tail])) {
            // single segment snake
            return $coords;
        }

        $body = strtolower($tail);

        if(!isset($snake_segments[$body])) {
            // two segment snake
            $coords[] = $snake_segments[$tail][0];
            return $coords;
        }

        return $coords;
    }
}