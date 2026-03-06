<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Racecourse $racecourse;

    #[ORM\Column(length: 50)]
    private string $raceType;

    #[ORM\Column(length: 50)]
    private string $discipline;

    #[ORM\Column]
    private int $distance;

    #[ORM\Column(length: 30)]
    private string $groundCondition;

    #[ORM\Column(type: 'float')]
    private float $prizeMoney;

    #[ORM\Column]
    private int $runnerCount;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $date): self { $this->date = $date; return $this; }
    public function getRacecourse(): Racecourse { return $this->racecourse; }
    public function setRacecourse(Racecourse $racecourse): self { $this->racecourse = $racecourse; return $this; }
    public function getRaceType(): string { return $this->raceType; }
    public function setRaceType(string $raceType): self { $this->raceType = $raceType; return $this; }
    public function getDiscipline(): string { return $this->discipline; }
    public function setDiscipline(string $discipline): self { $this->discipline = $discipline; return $this; }
    public function getDistance(): int { return $this->distance; }
    public function setDistance(int $distance): self { $this->distance = $distance; return $this; }
    public function getGroundCondition(): string { return $this->groundCondition; }
    public function setGroundCondition(string $groundCondition): self { $this->groundCondition = $groundCondition; return $this; }
    public function getPrizeMoney(): float { return $this->prizeMoney; }
    public function setPrizeMoney(float $prizeMoney): self { $this->prizeMoney = $prizeMoney; return $this; }
    public function getRunnerCount(): int { return $this->runnerCount; }
    public function setRunnerCount(int $runnerCount): self { $this->runnerCount = $runnerCount; return $this; }
}
