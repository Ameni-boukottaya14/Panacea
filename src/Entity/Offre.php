<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $NomO = null;

    #[ORM\Column(length: 255)]
    private ?string $DescriptionO = null;

    #[ORM\Column]
    private ?float $PrixO = null;

    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'Offre')]
    private Collection $abonnements;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomO(): ?string
    {
        return $this->NomO;
    }

    public function setNomO(string $NomO): static
    {
        $this->NomO = $NomO;

        return $this;
    }

    public function getDescriptionO(): ?string
    {
        return $this->DescriptionO;
    }

    public function setDescriptionO(string $DescriptionO): static
    {
        $this->DescriptionO = $DescriptionO;

        return $this;
    }

    public function getPrixO(): ?float
    {
        return $this->PrixO;
    }

    public function setPrixO(float $PrixO): static
    {
        $this->PrixO = $PrixO;

        return $this;
    }


    public function __toString()
    {
        return $this->getNomO();
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements->add($abonnement);
            $abonnement->setOffre($this);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        if ($this->abonnements->removeElement($abonnement)) {
            // set the owning side to null (unless already changed)
            if ($abonnement->getOffre() === $this) {
                $abonnement->setOffre(null);
            }
        }

        return $this;
    }
}