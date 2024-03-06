<?php

namespace App\Entity;

use App\Repository\OrdonnanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdonnanceRepository::class)]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nomMalade = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 20)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'ordonnances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pharmacie $pharmacie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $medicaments = null;

    #[ORM\Column(length: 20)]
    private ?string $prenomMalade = null;

    #[ORM\Column(length: 20)]
    private ?string $medecinTraitant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMalade(): ?string
    {
        return $this->nomMalade;
    }

    public function setNomMalade(string $nomMalade): static
    {
        $this->nomMalade = $nomMalade;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPharmacie(): ?Pharmacie
    {
        return $this->pharmacie;
    }

    public function setPharmacie(?Pharmacie $pharmacie): static
    {
        $this->pharmacie = $pharmacie;

        return $this;
    }

    public function getMedicaments(): ?string
    {
        return $this->medicaments;
    }

    public function setMedicaments(string $medicaments): static
    {
        $this->medicaments = $medicaments;

        return $this;
    }

    public function getPrenomMalade(): ?string
    {
        return $this->prenomMalade;
    }

    public function setPrenomMalade(string $prenomMalade): static
    {
        $this->prenomMalade = $prenomMalade;

        return $this;
    }

    public function getMedecinTraitant(): ?string
    {
        return $this->medecinTraitant;
    }

    public function setMedecinTraitant(string $medecinTraitant): static
    {
        $this->medecinTraitant = $medecinTraitant;

        return $this;
    }
}