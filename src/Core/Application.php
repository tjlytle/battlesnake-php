<?php

declare(strict_types=1);

namespace BattleSnake\Core;

use BattleSnake\Eventsource\EventRepository;
use BattleSnake\Eventsource\Payload;
use BattleSnake\Handler\EndHandler;
use BattleSnake\Handler\InfoHandler;
use BattleSnake\Handler\MoveHandler;
use BattleSnake\Handler\NotFoundHandler;
use BattleSnake\Handler\StartHandler;
use BattleSnake\Middleware\DispatchMiddleware;
use BattleSnake\Middleware\JsonParser;
use BattleSnake\Projection\GameStatListener;
use BattleSnake\Root\RootRepository;
use Crell\Serde\SerdeCommon;
use Crell\Tukio\Dispatcher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application implements ListenerProviderInterface
{
    public readonly Connection $connection;
    public readonly EntityManager $entity_manager;
    public readonly Dispatcher $event_dispatcher;
    private QueueRequestHandler $queue_handler;
    public readonly RootRepository $root_repository;

    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        public readonly Config $config,
    )
    {
        $this->setupDatabase();
        $this->setupEventsourcing();
        $this->setupHandlers();
    }

    private function setupDatabase(): void
    {
        $dsn_parser = new DsnParser(['mysql' => 'pdo_mysql']);
        $connection_params = $dsn_parser->parse($this->config->dsn);
        $this->connection = DriverManager::getConnection($connection_params);

        // Setup Doctrine ORM configuration
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../Projection'], // Entity directory paths
            true // Dev mode
        );

        // Create the entity manager with the existing connection
        $this->entity_manager = new EntityManager($this->connection, $config);
    }

    #[\Override]
    public function getListenersForEvent(object $event): iterable
    {
        if (!($event instanceof Payload)) {
            return [];
        }

        return match($event->event::class) {
            'BattleSnake\Event\End' => [
                new GameStatListener($this->entity_manager),
            ],
            default => [],
        };
   }

    private function setupEventsourcing(): void
    {
        // Add event dispatcher / listener
        $this->event_dispatcher = new Dispatcher($this);

        $serde = new SerdeCommon();
        $event_repository = new EventRepository(
            $this->connection,
            $serde,
        );

        $this->root_repository = new RootRepository(
            $event_repository,
            $this->event_dispatcher,
        );

    }

    private function setupHandlers(): void
    {
        // Create the API dispatch middleware
        $api_dispatch = new DispatchMiddleware();

        // Add routes
        $api_dispatch->addRoute('/', new InfoHandler($this->response_factory));
        $api_dispatch->addRoute(
            '/start',
            new StartHandler(
                $this->response_factory,
                $this->root_repository,
            ),
        );
        $api_dispatch->addRoute(
            '/move',
            new MoveHandler(
                $this->response_factory,
                $this->root_repository,
            ),
        );
        $api_dispatch->addRoute(
            '/end',
            new EndHandler(
                $this->response_factory,
                $this->root_repository
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
