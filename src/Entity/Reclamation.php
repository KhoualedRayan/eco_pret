<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne]
    private ?Transaction $transaction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_poste = null;

    #[ORM\Column(length: 2047, nullable: true)]
    private ?string $reponse = null;

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getDatePoste(): ?\DateTimeInterface
    {
        return $this->date_poste;
    }

    public function setDatePoste(\DateTimeInterface $date_poste): static
    {
        $this->date_poste = $date_poste;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }
}
