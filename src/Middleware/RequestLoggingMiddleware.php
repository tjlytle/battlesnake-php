<?php

namespace BattleSnake\Middleware;

use Laminas\Diactoros\Request\Serializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestLoggingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        error_log('Request:');
        error_log(Serializer::toString($request));
        return $handler->handle($request);
    }
}