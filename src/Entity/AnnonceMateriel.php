<?php

namespace App\Entity;

use App\Repository\AnnonceMaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceMaterielRepository::class)]
class AnnonceMateriel extends Annonce
{

    #[ORM\Column(length: 255)]
    private ?string $duree = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }



}
