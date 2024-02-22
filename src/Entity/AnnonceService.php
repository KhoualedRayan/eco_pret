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

    #[ORM\OneToMany(mappedBy: 'dateponcts', targetEntity: DatePonctuelleService::class, orphanRemoval: true)]
    private Collection $datePoncts;

    public function __construct()
    {
        $this->recurrences = new ArrayCollection();
        $this->datePoncts = new ArrayCollection();
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

    public function getType(): String
    {
        return "Service";
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

    /**
     * @return Collection<int, DatePonctuelleService>
     */
    public function getDatePoncts(): Collection
    {
        return $this->datePoncts;
    }

    public function addDatePonct(DatePonctuelleService $datePonct): static
    {
        if (!$this->datePoncts->contains($datePonct)) {
            $this->datePoncts->add($datePonct);
            $datePonct->setDateponcts($this);
        }

        return $this;
    }

    public function removeDatePonct(DatePonctuelleService $datePonct): static
    {
        if ($this->datePoncts->removeElement($datePonct)) {
            // set the owning side to null (unless already changed)
            if ($datePonct->getDateponcts() === $this) {
                $datePonct->setDateponcts(null);
            }
        }

        return $this;
    }

}
