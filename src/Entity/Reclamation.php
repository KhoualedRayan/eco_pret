<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $objet = null;

    #[ORM\Column(length: 2047)]
    private ?string $description = null;

    #[ORM\Column(length: 127)]
    private ?string $statut_reclamation = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations_envoyees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations_traitees')]
    private ?User $administrateur = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieReclamation $tag_reclamation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Annonce $annonce_litige = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): static
    {
        $this->objet = $objet;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatutReclamation(): ?string
    {
        return $this->statut_reclamation;
    }

    public function setStatutReclamation(string $statut_reclamation): static
    {
        $this->statut_reclamation = $statut_reclamation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAdministrateur(): ?User
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?User $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getTagReclamation(): ?CategorieReclamation
    {
        return $this->tag_reclamation;
    }

    public function setTagReclamation(?CategorieReclamation $tag_reclamation): static
    {
        $this->tag_reclamation = $tag_reclamation;

        return $this;
    }

    public function getAnnonceLitige(): ?Annonce
    {
        return $this->annonce_litige;
    }

    public function setAnnonceLitige(?Annonce $annonce_litige): static
    {
        $this->annonce_litige = $annonce_litige;

        return $this;
    }
}
