<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte associé a cet e-mail.')]
#[UniqueEntity(fields: ['username'], message: 'Ce pseudo est déjà utilisé par un autre utilisateur.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255, unique : true)]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sleep_mode = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Abonnement $abonnement = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Disponibilite::class, orphanRemoval: true)]
    private Collection $disponibilites;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class, orphanRemoval: true)]
    private Collection $reclamations_envoyees;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Reclamation::class)]
    private Collection $reclamations_traitees;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $demandes;

    #[ORM\Column(nullable: true)]
    private ?int $nb_florains = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAbonnement = null;

    #[ORM\ManyToOne]
    private ?Abonnement $nextAbonnement = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FileAttente::class, orphanRemoval: true)]
    private Collection $annoncesOuJattends;

    #[ORM\OneToMany(mappedBy: 'posteur', targetEntity: Annonce::class, orphanRemoval: true)]
    private Collection $annonces;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;

    public function __construct()
    {
        $this->disponibilites = new ArrayCollection();
        $this->reclamations_envoyees = new ArrayCollection();
        $this->reclamations_traitees = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->annoncesOuJattends = new ArrayCollection();
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles = array();

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isSleepMode(): ?bool
    {
        return $this->sleep_mode;
    }

    public function setSleepMode(?bool $sleep_mode): static
    {
        $this->sleep_mode = $sleep_mode;

        return $this;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): static
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    /**
     * @return Collection<int, Disponibilite>
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilite $disponibilite): static
    {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites->add($disponibilite);
            $disponibilite->setUser($this);
        }

        return $this;
    }

    public function clearDisponibilites(): static
{
    foreach ($this->disponibilites as $disponibilite) {
        $this->removeDisponibilite($disponibilite);
    }

    return $this;
}

    public function IsBusy()
    {
        foreach ($this->disponibilites as $disponibilite) {
            if($disponibilite->isBusyToday()){
                return true;
            }
        }
        return false;
    }


    public function removeDisponibilite(Disponibilite $disponibilite): static
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            // set the owning side to null (unless already changed)
            if ($disponibilite->getUser() === $this) {
                $disponibilite->setUser(null);
            }
        }

        return $this;
    }

    public function removeDisponibiliteByDate(\DateTimeInterface $date): static
    {
        foreach ($this->disponibilites as $disponibilite) {
            if ($disponibilite->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                $this->removeDisponibilite($disponibilite);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamationsEnvoyees(): Collection
    {
        return $this->reclamations_envoyees;
    }

    public function addReclamationsEnvoyee(Reclamation $reclamationsEnvoyee): static
    {
        if (!$this->reclamations_envoyees->contains($reclamationsEnvoyee)) {
            $this->reclamations_envoyees->add($reclamationsEnvoyee);
            $reclamationsEnvoyee->setUser($this);
        }

        return $this;
    }

    public function removeReclamationsEnvoyee(Reclamation $reclamationsEnvoyee): static
    {
        if ($this->reclamations_envoyees->removeElement($reclamationsEnvoyee)) {
            // set the owning side to null (unless already changed)
            if ($reclamationsEnvoyee->getUser() === $this) {
                $reclamationsEnvoyee->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamationsTraitees(): Collection
    {
        return $this->reclamations_traitees;
    }

    public function addReclamationsTraitee(Reclamation $reclamationsTraitee): static
    {
        if (!$this->reclamations_traitees->contains($reclamationsTraitee)) {
            $this->reclamations_traitees->add($reclamationsTraitee);
            $reclamationsTraitee->setAdministrateur($this);
        }

        return $this;
    }

    public function removeReclamationsTraitee(Reclamation $reclamationsTraitee): static
    {
        if ($this->reclamations_traitees->removeElement($reclamationsTraitee)) {
            // set the owning side to null (unless already changed)
            if ($reclamationsTraitee->getAdministrateur() === $this) {
                $reclamationsTraitee->setAdministrateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Transaction $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setClient($this);
        }

        return $this;
    }

    public function removeDemande(Transaction $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getClient() === $this) {
                $demande->setClient(null);
            }
        }

        return $this;
    }

    public function getNbFlorains(): ?int
    {
        return $this->nb_florains;
    }

    public function setNbFlorains(?int $nb_florains): static
    {
        $this->nb_florains = $nb_florains;

        return $this;
    }

    public function getDateAbonnement(): ?\DateTimeInterface
    {
        return $this->dateAbonnement;
    }

    public function setDateAbonnement(?\DateTimeInterface $dateAbonnement): static
    {
        $this->dateAbonnement = $dateAbonnement;

        return $this;
    }

    public function getNextAbonnement(): ?Abonnement
    {
        return $this->nextAbonnement;
    }

    public function setNextAbonnement(?Abonnement $nextAbonnement): static
    {
        $this->nextAbonnement = $nextAbonnement;

        return $this;
    }
    /**
     * @return Collection|FileAttente[]
     */
    public function getAnnoncesOuJattends(): Collection
    {
        return $this->annoncesOuJattends;
    }

    public function addAnnonceOuJattends(FileAttente $annonceOuJattends): self
    {
        if (!$this->annoncesOuJattends->contains($annonceOuJattends)) {
            $this->annoncesOuJattends[] = $annonceOuJattends;
            $annonceOuJattends->setUser($this);
        }

        return $this;
    }

    public function removeAnnonceOuJattends(FileAttente $annonceOuJattends): self
    {
        if ($this->annoncesOuJattends->removeElement($annonceOuJattends)) {
            // Si la relation était configurée pour être bidirectionnelle, définissez le côté opposé à null (sauf si déjà changé)
            if ($annonceOuJattends->getUser() === $this) {
                $annonceOuJattends->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setPosteur($this);
        }
        return $this;

    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // Si la relation était configurée pour être bidirectionnelle, définissez le côté opposé à null (sauf si déjà changé)
            if ($annonce->getPosteur() === $this) {
                $annonce->setPosteur(null);
            }
        }

        return $this;
    }
    public function contientUneNotifNonLu():bool{
        foreach($this->notifications as $notif){
            if(!$notif->isAEteLu()){
                return true;
            }
        }
        return false;
    }
    public function contientUneNotifLu():bool{
        foreach($this->notifications as $notif){
            if(!$notif->isAEteLu() == false){
                return true;
            }
        }
        return false;
    }
    public function contientUnMsgNonLu(){
        foreach($this->demandes as $transaction){
            if($transaction->getMessages()->last()){
                if($transaction->getMessages()->last()->getExpediteur() != $this){
                    if(!$transaction->getMessages()->last()->isAEteLu()){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // public function getTransactionsWherePosteur() {
    //     return $this->getUser()->getAnnonces()
    //                 ->filter(function($a) {
    //                             return $a->getTransaction() != null;
    //                         })
    //                 ->map(function($a) { return $a->getTransaction(); });
    // }

    // public function getTransactions() {
    //     return new ArrayCollection((array)getTransactionsWherePosteur()+$demandes);
    // }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

}
