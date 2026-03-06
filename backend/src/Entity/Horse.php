<?php

namespace App\Entity;

use App\Repository\HorseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HorseRepository::class)]
class Horse
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column]
    private int $age;

    #[ORM\Column(length: 20)]
    private string $sex;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Trainer $trainer;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private JockeyOrDriver $habitualJockeyOrDriver;

    #[ORM\Column(type: 'float')]
    private float $totalEarnings = 0;

    #[ORM\Column(length: 20)]
    private string $recentForm = '00000';

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getAge(): int { return $this->age; }
    public function setAge(int $age): self { $this->age = $age; return $this; }
    public function getSex(): string { return $this->sex; }
    public function setSex(string $sex): self { $this->sex = $sex; return $this; }
    public function getTrainer(): Trainer { return $this->trainer; }
    public function setTrainer(Trainer $trainer): self { $this->trainer = $trainer; return $this; }
    public function getHabitualJockeyOrDriver(): JockeyOrDriver { return $this->habitualJockeyOrDriver; }
    public function setHabitualJockeyOrDriver(JockeyOrDriver $j): self { $this->habitualJockeyOrDriver = $j; return $this; }
    public function getTotalEarnings(): float { return $this->totalEarnings; }
    public function setTotalEarnings(float $totalEarnings): self { $this->totalEarnings = $totalEarnings; return $this; }
    public function getRecentForm(): string { return $this->recentForm; }
    public function setRecentForm(string $recentForm): self { $this->recentForm = $recentForm; return $this; }
}
