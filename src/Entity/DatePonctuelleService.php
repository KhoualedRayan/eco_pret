<?php

namespace App\Entity;

use App\Repository\DatePonctuelleServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DatePonctuelleServiceRepository::class)]
class DatePonctuelleService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'datePoncts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnnonceService $dateponcts = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDateponcts(): ?AnnonceService
    {
        return $this->dateponcts;
    }

    public function setDateponcts(?AnnonceService $dateponcts): static
    {
        $this->dateponcts = $dateponcts;

        return $this;
    }
}
