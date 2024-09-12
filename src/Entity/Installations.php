<?php

namespace App\Entity;

use App\Repository\InstallationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationsRepository::class)]
class Installations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'installations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle= null;

    #[ORM\Column(nullable: true)]
    private ?int $numero_siege = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description_probleme = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_signalement = null;

    #[ORM\Column]
    private ?bool $etat_reparation = null;

    #[ORM\ManyToOne(inversedBy: 'installations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $employe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getNumeroSiege(): ?int
    {
        return $this->numero_siege;
    }

    public function setNumeroSiege(?int $numero_siege): static
    {
        $this->numero_siege = $numero_siege;

        return $this;
    }

    public function getDescriptionProbleme(): ?string
    {
        return $this->description_probleme;
    }

    public function setDescriptionProbleme(string $description_probleme): static
    {
        $this->description_probleme = $description_probleme;

        return $this;
    }

    public function getDateSignalement(): ?\DateTimeInterface
    {
        return $this->date_signalement;
    }

    public function setDateSignalement(\DateTimeInterface $date_signalement): static
    {
        $this->date_signalement = $date_signalement;

        return $this;
    }

    public function isEtatReparation(): ?bool
    {
        return $this->etat_reparation;
    }

    public function setEtatReparation(bool $etat_reparation): static
    {
        $this->etat_reparation = $etat_reparation;

        return $this;
    }

    public function getEmploye(): ?Utilisateur
    {
        return $this->employe;
    }

    public function setEmploye(?Utilisateur $employe): static
    {
        $this->employe = $employe;

        return $this;
    }
}
