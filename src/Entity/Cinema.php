<?php

namespace App\Entity;

use App\Repository\CinemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CinemaRepository::class)]
class Cinema
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $GSM = null;

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'cinema', orphanRemoval: true)]
    private Collection $seances;

    /**
     * @var Collection<int, Diffusion>
     */
    #[ORM\OneToMany(targetEntity: Diffusion::class, mappedBy: 'cinemas', orphanRemoval: true)]
    private Collection $diffusions;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->diffusions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getGSM(): ?string
    {
        return $this->GSM;
    }

    public function setGSM(string $GSM): static
    {
        $this->GSM = $GSM;

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): static
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
            $seance->setCinema($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): static
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getCinema() === $this) {
                $seance->setCinema(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Diffusion>
     */
    public function getDiffusions(): Collection
    {
        return $this->diffusions;
    }

    public function addDiffusion(Diffusion $diffusion): static
    {
        if (!$this->diffusions->contains($diffusion)) {
            $this->diffusions->add($diffusion);
            $diffusion->setCinemas($this);
        }

        return $this;
    }

    public function removeDiffusion(Diffusion $diffusion): static
    {
        if ($this->diffusions->removeElement($diffusion)) {
            // set the owning side to null (unless already changed)
            if ($diffusion->getCinemas() === $this) {
                $diffusion->setCinemas(null);
            }
        }

        return $this;
    }

}
