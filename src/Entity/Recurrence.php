<?php

namespace App\Entity;

use App\Repository\RecurrenceRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\ManyToOne(inversedBy: 'recurrence')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnnonceService $annonceServ = null;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getAnnonceServ(): ?AnnonceService
    {
        return $this->annonceServ;
    }

    public function setAnnonceServ(?AnnonceService $annonceServ): static
    {
        $this->annonceServ = $annonceServ;

        return $this;
    }
}
