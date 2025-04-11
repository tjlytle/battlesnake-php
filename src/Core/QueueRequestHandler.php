<?php

declare(strict_types=1);

namespace BattleSnake\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QueueRequestHandler implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] */
    private array $middleware = [];

    public function __construct(private readonly RequestHandlerInterface $fallback_handler)
    {
    }
    
    public function add(MiddlewareInterface $middleware): void
    {
        $this->middleware[] = $middleware;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Last middleware in the queue has called on the request handler.
        if (0 === count($this->middleware)) {
            return $this->fallback_handler->handle($request);
        }
        
        $middleware = array_shift($this->middleware);
        return $middleware->process($request, $this);
    }
} 