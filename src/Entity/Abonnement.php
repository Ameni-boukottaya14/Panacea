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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateC = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateE = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'abonnements')]
    private ?Offre $Offre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->DateC;
    }

    public function setDateC(?\DateTimeInterface $DateC): static
    {
        $this->DateC = $DateC;

        return $this;
    }

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->DateE;
    }

    public function setDateE(?\DateTimeInterface $DateE): static
    {
        $this->DateE = $DateE;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->Offre;
    }

    public function setOffre(?Offre $Offre): static
    {
        $this->Offre = $Offre;

        return $this;
    }
}
