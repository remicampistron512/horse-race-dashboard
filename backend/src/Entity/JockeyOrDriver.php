<?php

namespace App\Entity;

use App\Repository\JockeyOrDriverRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JockeyOrDriverRepository::class)]
class JockeyOrDriver
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private string $firstName;

    #[ORM\Column(length: 80)]
    private string $lastName;

    #[ORM\Column(length: 50)]
    private string $discipline;

    public function getId(): ?int { return $this->id; }
    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }
    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }
    public function getDiscipline(): string { return $this->discipline; }
    public function setDiscipline(string $discipline): self { $this->discipline = $discipline; return $this; }
    public function getFullName(): string { return trim($this->firstName . ' ' . $this->lastName); }
}
