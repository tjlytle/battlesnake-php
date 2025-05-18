<?php

declare(strict_types=1);

namespace BattleSnake\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonParser implements MiddlewareInterface
{
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('content-type');
        if (\str_contains($contentType, 'application/json')) {
            $body = (string)$request->getBody();
            $request->getBody()->rewind();

            if (! empty($body)) {
                $parsedBody = \json_decode($body, true);
                $request = $request->withParsedBody($parsedBody);
            }
        }

        return $handler->handle($request);
    }
}
