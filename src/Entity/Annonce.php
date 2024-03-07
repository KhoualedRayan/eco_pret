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

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: FileAttente::class, orphanRemoval: true)]
    private Collection $attentes;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->attentes = new ArrayCollection();
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


    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAnnonce($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAnnonce() === $this) {
                $transaction->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FileAttente>
     */
    public function getAttentes(): Collection
    {
        return $this->attentes;
    }

    public function addAttente(FileAttente $attente): static
    {
        if (!$this->attentes->contains($attente)) {
            $this->attentes->add($attente);
            $attente->setAnnonce($this);
        }

        return $this;
    }

    public function removeAttente(FileAttente $attente): static
    {
        if ($this->attentes->removeElement($attente)) {
            // set the owning side to null (unless already changed)
            if ($attente->getAnnonce() === $this) {
                $attente->setAnnonce(null);
            }
        }

        return $this;
    }

}
