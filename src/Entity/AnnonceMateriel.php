<?php

namespace App\Entity;

use App\Repository\AnnonceMaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceMaterielRepository::class)]
class AnnonceMateriel extends Annonce
{

    #[ORM\Column(length: 255)]
    private ?string $duree = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $posteur = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieMateriel $categorie = null;


    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

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

}
