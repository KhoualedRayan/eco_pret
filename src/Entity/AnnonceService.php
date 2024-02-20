<?php

namespace App\Entity;

use App\Repository\AnnonceServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Annonce;
#[ORM\Entity(repositoryClass: AnnonceServiceRepository::class)]
class AnnonceService extends Annonce
{

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Recurrence $id_recurrence = null;

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

}
