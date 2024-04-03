<?php

namespace App\Entity;

use App\Repository\AnnonceServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use \DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceServiceRepository::class)]
class AnnonceService extends Annonce
{

    #[ORM\OneToMany(mappedBy: 'dateponcts', targetEntity: DatePonctuelleService::class, orphanRemoval: true)]
    private Collection $datePoncts;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieService $categorie = null;

    #[ORM\OneToMany(mappedBy: 'annonceServ', targetEntity: Recurrence::class, orphanRemoval: true)]
    private Collection $recurrence;

    public function __construct()
    {
        $this->recurrences = new ArrayCollection();
        $this->datePoncts = new ArrayCollection();
        $this->recurrence = new ArrayCollection();
    }


    

    public function getType(): String
    {
        return "Service";
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

    public function getCategorie(): ?CategorieService
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieService $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Recurrence>
     */
    public function getRecurrence(): Collection
    {
        return $this->recurrence;
    }

    public function addRecurrence(Recurrence $recurrence): static
    {
        if (!$this->recurrence->contains($recurrence)) {
            $this->recurrence->add($recurrence);
            $recurrence->setAnnonceServ($this);
        }

        return $this;
    }

    public function removeRecurrence(Recurrence $recurrence): static
    {
        if ($this->recurrence->removeElement($recurrence)) {
            // set the owning side to null (unless already changed)
            if ($recurrence->getAnnonceServ() === $this) {
                $recurrence->setAnnonceServ(null);
            }
        }

        return $this;
    }
    public function dateFin(): \DateTime
    {
        $a = max(
            array_merge(
                $this->datePoncts->map(function ($a) { return $a->getDate(); })->toArray(), 
                $this->recurrence->map(function ($a) { return $a->getDateFin(); })->toArray()
            )
        );
        return $a;
    }

}
