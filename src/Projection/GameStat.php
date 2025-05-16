<?php

namespace BattleSnake\Projection;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'game_stats')]
class GameStat
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $aggregate_id;

    #[ORM\Column(type: 'boolean')]
    private bool $win;

    public function __construct(UuidInterface $aggregate_id, bool $win)
    {
        $this->aggregate_id = $aggregate_id->toString();
        $this->win = $win;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAggregateId(): UuidInterface
    {
        return Uuid::fromString($this->aggregate_id);
    }

    public function isWin(): bool
    {
        return $this->win;
    }
}
