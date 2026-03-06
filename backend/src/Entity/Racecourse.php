<?php

namespace App\Entity;

use App\Repository\RacecourseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RacecourseRepository::class)]
class Racecourse
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(length: 80)]
    private string $region;

    #[ORM\Column(length: 50)]
    private string $surface;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getRegion(): string { return $this->region; }
    public function setRegion(string $region): self { $this->region = $region; return $this; }
    public function getSurface(): string { return $this->surface; }
    public function setSurface(string $surface): self { $this->surface = $surface; return $this; }
}
