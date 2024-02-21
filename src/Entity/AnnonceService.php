<?php

namespace App\Entity;

use App\Repository\AnnonceServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceServiceRepository::class)]
class AnnonceService extends Annonce
{
    #[ORM\OneToMany(mappedBy: 'annonceService', targetEntity: Recurrence::class, cascade: ['persist', 'remove'])]
    private Collection $recurrences;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $posteur = null;

    public function __construct()
    {
        $this->recurrences = new ArrayCollection();
    }

    public function getRecurrences(): Collection
    {
        return $this->recurrences;
    }

    public function addRecurrence(Recurrence $recurrence): self
    {
        if (!$this->recurrences->contains($recurrence)) {
            $this->recurrences[] = $recurrence;
            $recurrence->setAnnonceService($this);
        }

        return $this;
    }

    public function removeRecurrence(Recurrence $recurrence): self
    {
        if ($this->recurrences->removeElement($recurrence)) {
            // set the owning side to null (unless already changed)
            if ($recurrence->getAnnonceService() === $this) {
                $recurrence->setAnnonceService(null);
            }
        }

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }


}