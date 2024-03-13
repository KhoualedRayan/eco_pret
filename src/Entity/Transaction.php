<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Annonce;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_transaction = null;

    #[ORM\Column(length: 127)]
    private ?string $statut_transaction = null;

    #[ORM\Column(nullable: true)]
    private ?int $note_client = null;

    #[ORM\Column(length: 1023, nullable: true)]
    private ?string $commentaire_client = null;

    #[ORM\Column(nullable: true)]
    private ?int $note_offrant = null;

    #[ORM\Column(length: 1023, nullable: true)]
    private ?string $commentaire_offrant = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToOne(mappedBy: 'transaction')]
    private ?Annonce $annonce = null;

    #[ORM\Column(nullable: true)]
    private ?int $prix_final = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duree_final = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebutPret = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->date_transaction;
    }

    public function setDateTransaction(?\DateTimeInterface $date_transaction): static
    {
        $this->date_transaction = $date_transaction;

        return $this;
    }

    public function getStatutTransaction(): ?string
    {
        return $this->statut_transaction;
    }

    public function setStatutTransaction(string $statut_transaction): static
    {
        $this->statut_transaction = $statut_transaction;

        return $this;
    }

    public function getNoteClient(): ?int
    {
        return $this->note_client;
    }

    public function setNoteClient(?int $note_client): static
    {
        $this->note_client = $note_client;

        return $this;
    }

    public function getCommentaireClient(): ?string
    {
        return $this->commentaire_client;
    }

    public function setCommentaireClient(?string $commentaire_client): static
    {
        $this->commentaire_client = $commentaire_client;

        return $this;
    }

    public function getNoteOffrant(): ?int
    {
        return $this->note_offrant;
    }

    public function setNoteOffrant(?int $note_offrant): static
    {
        $this->note_offrant = $note_offrant;

        return $this;
    }

    public function getCommentaireOffrant(): ?string
    {
        return $this->commentaire_offrant;
    }

    public function setCommentaireOffrant(?string $commentaire_offrant): static
    {
        $this->commentaire_offrant = $commentaire_offrant;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setTransaction($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getTransaction() === $this) {
                $message->setTransaction(null);
            }
        }

        return $this;
    }

    public function contientUserDansAnnonce(?User $user, ?Annonce $annonce): bool{

        if ($this->client->getId() == $user->getId() && $this->annonce->getId() == $annonce->getId())
            return true;
        if ($this->posteur->getId() == $user->getId() && $this->annonce->getId() == $annonce->getId())
            return true;
        return false;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): static
    {
        // unset the owning side of the relation if necessary
        if ($annonce === null && $this->annonce !== null) {
            $this->annonce->setTransaction(null);
        }

        // set the owning side of the relation if necessary
        if ($annonce !== null && $annonce->getTransaction() !== $this) {
            $annonce->setTransaction($this);
        }

        $this->annonce = $annonce;

        return $this;
    }

    public function getPrixFinal(): ?int
    {
        return $this->prix_final;
    }

    public function setPrixFinal(?int $prix_final): static
    {
        $this->prix_final = $prix_final;

        return $this;
    }

    public function getDureeFinal(): ?string
    {
        return $this->duree_final;
    }

    public function setDureeFinal(?string $duree_final): static
    {
        $this->duree_final = $duree_final;

        return $this;
    }

    public function getRole(?User $user): string
    {
        if ($this->getClient() != $user) return "Offrant";
        else return "Client";
    }

    public function getDateDebutPret(): ?\DateTimeInterface
    {
        return $this->dateDebutPret;
    }

    public function setDateDebutPret(?\DateTimeInterface $dateDebutPret): static
    {
        $this->dateDebutPret = $dateDebutPret;

        return $this;
    }
}
