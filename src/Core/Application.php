<?php

declare(strict_types=1);

namespace BattleSnake\Core;

use BattleSnake\Handler\EndHandler;
use BattleSnake\Handler\InfoHandler;
use BattleSnake\Handler\MoveHandler;
use BattleSnake\Handler\NotFoundHandler;
use BattleSnake\Handler\StartHandler;
use BattleSnake\Middleware\DispatchMiddleware;
use BattleSnake\Middleware\RequestLoggingMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Application
{
    private QueueRequestHandler $queue_handler;

    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly ServerRequestFactoryInterface $server_request_factory,
        private readonly StreamFactoryInterface $stream_factory,
    )
    {
        $this->setupHandlers();
    }

    private function setupHandlers(): void
    {
        // Create the API dispatch middleware
        $api_dispatch = new DispatchMiddleware();

        // Add routes
        $api_dispatch->addRoute('/', new InfoHandler($this->response_factory));
        $api_dispatch->addRoute('/start', new StartHandler($this->response_factory));
        $api_dispatch->addRoute('/move', new MoveHandler($this->response_factory));
        $api_dispatch->addRoute('/end', new EndHandler($this->response_factory));

        // Create the fallback handler
        $fallback_handler = new NotFoundHandler($this->response_factory);

        // Create the queue request handler
        $this->queue_handler = new QueueRequestHandler($fallback_handler);

        $this->queue_handler->add(new RequestLoggingMiddleware());
        // Add the API dispatch middleware to the queue
        $this->queue_handler->add($api_dispatch);
    }

    private function requestFromGlobals(): ServerRequestInterface
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        $request = $this->server_request_factory->createServerRequest($method, $uri, $_SERVER);
        
        if ($method !== 'GET') {
            $body = file_get_contents('php://input');
            if ($body !== '') {
                $stream = $this->stream_factory->createStream($body);
                $request = $request->withBody($stream);
            }
        }
        
        return $request;
    }

    private function sendResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        
        // Send status line
        header(sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $statusCode,
            $reasonPhrase ? ' ' . $reasonPhrase : ''
        ), true, $statusCode);
        
        // Iterate over headers and send them, handling multi-value headers
        foreach ($response->getHeaders() as $name => $values) {
            $replace = true;
            foreach ($values as $value) {
                header("$name: $value", $replace);
                $replace = false;
            }
        }
        
        // Need to rewind the body before trying to send it
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }

        echo $response->getBody()->getContents();
    }

    public function run(): void
    {
        $request = $this->requestFromGlobals();
        $response = $this->queue_handler->handle($request);
        $this->sendResponse($response);
    }
}