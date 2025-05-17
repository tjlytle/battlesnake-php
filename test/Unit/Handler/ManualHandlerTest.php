<?php

namespace BattleSnake\Tests\Unit\Handler;

use BattleSnake\Domain\Direction;
use BattleSnake\Domain\GameState;
use BattleSnake\Domain\Parser\GameStateParserFactory;
use BattleSnake\Event\End;
use BattleSnake\Event\Start;
use BattleSnake\Event\Turn;
use BattleSnake\Tests\Unit\ApplicationProvider;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ManualHandlerTest extends TestCase
{
    use ApplicationProvider;

    #[Test]
    public function response_is_400_when_game_not_started(): void
    {
        $uuid = Uuid::uuid4();
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/manual/' . $uuid->toString());
        $request = $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode(['direction' => 'up']));
        $request->getBody()->rewind();

        $response = $this->getApplication()->getQueueHandler()->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    #[Test]
    public function response_is_400_when_game_ended(): void
    {
        $uuid = Uuid::uuid4();

        $game = $this->getApplication()->root_repository->retrieve($uuid);
        $game->addEvent(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
            new End(self::getJsonData('four-player-large-end')),
        );
        $this->getApplication()->root_repository->persist($game);

        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/manual/' . $uuid->toString());
        $request = $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode(['direction' => 'up']));
        $request->getBody()->rewind();

        $response = $this->getApplication()->getQueueHandler()->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    #[Test]
    public function response_is_200_when_manual_contro_allowed(): void
    {
        $uuid = Uuid::uuid4();

        $game = $this->getApplication()->root_repository->retrieve($uuid);
        $game->addEvent(
            new Start(self::getJsonData('four-player-large-start')),
            new Turn(self::getJsonData('four-player-large-move1')),
            new Turn(self::getJsonData('four-player-large-move2')),
        );
        $this->getApplication()->root_repository->persist($game);

        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/manual/' . $uuid->toString());
        $request = $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode(['direction' => 'up']));
        $request->getBody()->rewind();

        $response = $this->getApplication()->getQueueHandler()->handle($request);
        $this->assertEquals(202, $response->getStatusCode());

        $game = $this->getApplication()->root_repository->retrieve($uuid);
        self::assertSame(Direction::UP, $game->getLastNudge());
    }

    private static function getJsonData(string $string): GameState
    {
        $parser = GameStateParserFactory::make();
        $json = file_get_contents(__DIR__ . '/../../requests/' . $string . '.json');
        if ($json === false) {
            throw new \RuntimeException('Failed to read JSON file');
        }
        $data = json_decode($json, true);
        if ($data === null) {
            throw new \RuntimeException('Failed to decode JSON data');
        }
        return $parser->parse($data);
    }

}
