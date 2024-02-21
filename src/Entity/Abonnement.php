<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $CLinetId = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offre $OffreId = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateE = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateC = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCLinetId(): ?Client
    {
        return $this->CLinetId;
    }

    public function setCLinetId(?Client $CLinetId): static
    {
        $this->CLinetId = $CLinetId;

        return $this;
    }

    public function getOffreId(): ?Offre
    {
        return $this->OffreId;
    }

    public function setOffreId(?Offre $OffreId): static
    {
        $this->OffreId = $OffreId;

        return $this;
    }

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->DateE;
    }

    public function setDateE(\DateTimeInterface $DateE): static
    {
        $this->DateE = $DateE;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->DateC;
    }

    public function setDateC(\DateTimeInterface $DateC): static
    {
        $this->DateC = $DateC;

        return $this;
    }
}
