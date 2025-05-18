<?php

declare(strict_types=1);

namespace BattleSnake\Tests\Unit\Middleware;

use BattleSnake\Middleware\JsonParser as SUT;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonParserTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function process_parses_json_body(): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/not-checked');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write('{"key":"value"}');

        self::assertNull($request->getParsedBody());

        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $handler = $this->prophesize(\Psr\Http\Server\RequestHandlerInterface::class);
        $handler->handle(Argument::type(ServerRequestInterface::class))
            ->will(function ($arguments) use ($response) {
                $request = $arguments[0];
                TestCase::assertSame(['key' => 'value'], $request->getParsedBody());
                return $response;
            });

        $sut = new SUT();
        self::assertSame(
            $response,
            $sut->process($request, $handler->reveal()),
        );
    }
}
