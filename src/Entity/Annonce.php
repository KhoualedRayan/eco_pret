<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap(["materiel" => AnnonceMateriel::class, "service" => AnnonceService::class])]
abstract class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $posteur = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 1023, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $prix = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(length: 127)]
    private ?string $statut = null;

    #[ORM\OneToOne(inversedBy: 'annonce', cascade: ['persist', 'remove'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'annoncesOuJAttends')]
    private Collection $gensEnAttente;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->gensEnAttente = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

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

    /**
     * @return Collection<int, User>
     */
    public function getGensEnAttente(): Collection
    {
        return $this->gensEnAttente;
    }

    public function addGensEnAttente(User $gensEnAttente): static
    {
        if (!$this->gensEnAttente->contains($gensEnAttente)) {
            $this->gensEnAttente->add($gensEnAttente);
        }

        return $this;
    }

    public function removeGensEnAttente(User $gensEnAttente): static
    {
        $this->gensEnAttente->removeElement($gensEnAttente);

        return $this;
    }

}
