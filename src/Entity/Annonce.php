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

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }


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
    public function contientUserDansFiles(User $user): bool{
        foreach ($this->attentes as $fileAttente) {
            if ($fileAttente->getUser() == $user) {
                return true; // L'utilisateur est trouvé dans l'une des files, donc on retourne vrai immédiatement
            }
        }
        return false;
    }
    public function positionFileAttente(Annonce $annonce,User $user) :string{
        $compteur = 0;
        $resultat = "x/x";
        foreach ($this->getAttentes() as $file) {
            if ($file->getAnnonce($annonce)) {
                $resultat = $compteur ."";
            }
            $compteur++;
        }
        return $resultat;
    }

}
