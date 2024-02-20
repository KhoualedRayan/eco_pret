<?php

namespace App\Entity;

use App\Repository\RecurrenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecurrenceRepository::class)]
class Recurrence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeRecurrence = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeRecurrence(): ?string
    {
        return $this->typeRecurrence;
    }

    public function setTypeRecurrence(string $typeRecurrence): static
    {
        $this->typeRecurrence = $typeRecurrence;

        return $this;
    }
}
