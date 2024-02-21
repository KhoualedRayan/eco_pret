<?php

namespace App\Entity;

use App\Repository\AnnonceServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceServiceRepository::class)]
class AnnonceService extends Annonce
{

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Recurrence $id_recurrence = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $posteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_debut = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdRecurrence(): ?Recurrence
    {
        return $this->id_recurrence;
    }

    public function setIdRecurrence(?Recurrence $id_recurrence): static
    {
        $this->id_recurrence = $id_recurrence;

        return $this;
    }

    public function getPosteur(): ?User
    {
        return $this->posteur;
    }

    public function setPosteur(?User $posteur): static
    {
        $this->posteur = $posteur;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }


}