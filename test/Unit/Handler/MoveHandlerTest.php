<?php

namespace BattleSnake\Tests\Unit\Handler;

use BattleSnake\Handler\MoveHandler as SUT;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MoveHandlerTest extends TestCase
{
    #[Test]
    #[DataProvider('provideGameStateExamples')]
    public function handle_returns_valid_move(string $json): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/not-checked');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withParsedBody(json_decode($json, true));

        $sut = new SUT(new ResponseFactory());
        $response = $sut->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('{"move":"up","shout":"Moving up!"}', (string) $response->getBody());
    }

    public static function provideGameStateExamples(): \Generator
    {
        $example_requests = [
            'official-example',
            'four-player-large-move1',
            'four-player-large-move2',
            'four-player-large-move3',
            'four-player-large-move10',
            'official-example-v1',
            'official-example-v2',
        ];

        foreach ($example_requests as $label) {
            $json = file_get_contents(__DIR__ . "/../../requests/{$label}.json");
            if ($json === false) {
                throw new \RuntimeException("Failed to read JSON file for {$label}");
            }

            yield $label => [
                $json,
            ];
        }
    }
}
