<?php

declare(strict_types=1);

namespace BattleSnake\Core;

use BattleSnake\Eventsource\Repository;
use BattleSnake\Handler\EndHandler;
use BattleSnake\Handler\InfoHandler;
use BattleSnake\Handler\MoveHandler;
use BattleSnake\Handler\NotFoundHandler;
use BattleSnake\Handler\StartHandler;
use BattleSnake\Middleware\DispatchMiddleware;
use BattleSnake\Middleware\JsonParser;
use BattleSnake\Middleware\RequestLoggingMiddleware;
use Crell\Serde\SerdeCommon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application
{
    public readonly Connection $connection;
    private QueueRequestHandler $queue_handler;

    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        public readonly Config $config,
    )
    {
        $this->setupDatabase();
        $this->setupHandlers();
    }

    private function setupDatabase()
    {
        $dsn_parser = new DsnParser(['mysql' => 'pdo_mysql']);
        $connection_params = $dsn_parser->parse($this->config->dsn);
        $this->connection = DriverManager::getConnection($connection_params);
    }

    private function setupHandlers(): void
    {
        // Create the API dispatch middleware
        $api_dispatch = new DispatchMiddleware();

        $serde = new SerdeCommon();
        $repository = new Repository(
            $this->connection,
            $serde,
        );

        // Add routes
        $api_dispatch->addRoute('/', new InfoHandler($this->response_factory));
        $api_dispatch->addRoute(
            '/start',
            new StartHandler(
                $this->response_factory,
                $repository,
            ),
        );
        $api_dispatch->addRoute(
            '/move',
            new MoveHandler(
                $this->response_factory,
                $repository,
            ),
        );
        $api_dispatch->addRoute(
            '/end',
            new EndHandler(
                $this->response_factory,
                $repository
            ),
        );

        // Create the fallback handler
        $fallback_handler = new NotFoundHandler($this->response_factory);

        // Create the queue request handler
        $this->queue_handler = new QueueRequestHandler($fallback_handler);
        $this->queue_handler->add(new JsonParser());
        // Add the API dispatch middleware to the queue
        $this->queue_handler->add($api_dispatch);
    }

    private function requestFromGlobals(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
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

    public function getQueueHandler(): QueueRequestHandler
    {
        return $this->queue_handler;
    }

    public function run(): void
    {
        $request = $this->requestFromGlobals();
        $response = $this->queue_handler->handle($request);
        $this->sendResponse($response);
    }
}