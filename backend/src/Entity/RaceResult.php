<?php

namespace App\Entity;

use App\Repository\RaceResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceResultRepository::class)]
class RaceResult
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Race $race;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Horse $horse;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private JockeyOrDriver $jockeyOrDriver;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Trainer $trainer;

    #[ORM\Column(type: 'float')]
    private float $odds;

    #[ORM\Column]
    private int $finishPosition;

    #[ORM\Column(type: 'float')]
    private float $earnings;

    #[ORM\Column(nullable: true)]
    private ?int $ropeNumber = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $weightCarried = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $timeRecorded = null;

    public function getId(): ?int { return $this->id; }
    public function getRace(): Race { return $this->race; }
    public function setRace(Race $race): self { $this->race = $race; return $this; }
    public function getHorse(): Horse { return $this->horse; }
    public function setHorse(Horse $horse): self { $this->horse = $horse; return $this; }
    public function getJockeyOrDriver(): JockeyOrDriver { return $this->jockeyOrDriver; }
    public function setJockeyOrDriver(JockeyOrDriver $jockeyOrDriver): self { $this->jockeyOrDriver = $jockeyOrDriver; return $this; }
    public function getTrainer(): Trainer { return $this->trainer; }
    public function setTrainer(Trainer $trainer): self { $this->trainer = $trainer; return $this; }
    public function getOdds(): float { return $this->odds; }
    public function setOdds(float $odds): self { $this->odds = $odds; return $this; }
    public function getFinishPosition(): int { return $this->finishPosition; }
    public function setFinishPosition(int $finishPosition): self { $this->finishPosition = $finishPosition; return $this; }
    public function getEarnings(): float { return $this->earnings; }
    public function setEarnings(float $earnings): self { $this->earnings = $earnings; return $this; }
    public function getRopeNumber(): ?int { return $this->ropeNumber; }
    public function setRopeNumber(?int $ropeNumber): self { $this->ropeNumber = $ropeNumber; return $this; }
    public function getWeightCarried(): ?float { return $this->weightCarried; }
    public function setWeightCarried(?float $weightCarried): self { $this->weightCarried = $weightCarried; return $this; }
    public function getTimeRecorded(): ?string { return $this->timeRecorded; }
    public function setTimeRecorded(?string $timeRecorded): self { $this->timeRecorded = $timeRecorded; return $this; }
}
