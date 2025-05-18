<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Handler;

use BattleSnake\Tests\Unit\ApplicationProvider;
use Laminas\Diactoros\ServerRequestFactory;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MoveHandlerTest extends TestCase
{
    use ApplicationProvider;

    #[Test]
    #[DataProvider('provideGameStateExamples')]
    public function handle_returns_valid_move(string $json): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/move');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write($json);
        $request->getBody()->rewind();

        $response = $this->getApplication()->getQueueHandler()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $validator = new ValidatorBuilder()->fromYamlFile(__DIR__ . '/../../../open-api.yaml')->getResponseValidator();
        $validator->validate(new OperationAddress('/move', 'post'), $response);
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
            $json = \file_get_contents(__DIR__ . "/../../requests/{$label}.json");
            if ($json === false) {
                throw new \RuntimeException("Failed to read JSON file for {$label}");
            }

            yield $label => [
                $json,
            ];
        }
    }
}
