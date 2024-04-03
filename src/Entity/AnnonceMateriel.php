<?php

namespace App\Entity;

use App\Repository\AnnonceMaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceMaterielRepository::class)]
class AnnonceMateriel extends Annonce
{

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode = null;



    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieMateriel $categorie = null;

    #[ORM\Column(nullable: true)]
    private ?int $dureeH = null;


    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(string $duree): static
    {
        $this->mode = $duree;

        return $this;
    }
    public function setModeNull(): static
    {
        $this->mode = null;

        return $this;
    }


    public function getType(): String
    {
        return "Materiel";
    }

    public function getCategorie(): ?CategorieMateriel
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieMateriel $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDureeH(): ?int
    {
        return $this->dureeH;
    }

    public function setDureeH(?int $dureeH): static
    {
        $this->dureeH = $dureeH;

        return $this;
    }

}
