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

    #[ORM\Column(type: 'integer')]
    private int $length;

    #[ORM\Column(type: 'integer')]
    private int $survived;

    public function __construct(
        UuidInterface $aggregate_id,
        bool $win,
        int $length,
        int $survived,
    )
    {
        $this->aggregate_id = $aggregate_id->toString();
        $this->win = $win;
        $this->length = $length;
        $this->survived = $survived;
    }

    public function getId(): string
    {
        return $this->aggregate_id;
    }

    public function getAggregateId(): UuidInterface
    {
        return Uuid::fromString($this->aggregate_id);
    }

    public function isWin(): bool
    {
        return $this->win;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getSurvived(): int
    {
        return $this->survived;
    }
}
