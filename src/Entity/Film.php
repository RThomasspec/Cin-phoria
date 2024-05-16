<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $AgeMinimum = null;

    #[ORM\Column]
    private ?bool $coupDeCoeur = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(type: Types::BLOB)]
    private $affichage;

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'film', orphanRemoval: true)]
    private Collection $seances;

    /**
     * @var Collection<int, Diffusion>
     */
    #[ORM\OneToMany(targetEntity: Diffusion::class, mappedBy: 'films', orphanRemoval: true)]
    private Collection $diffusions;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'film')]
    private Collection $avis;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->diffusions = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAgeMinimum(): ?int
    {
        return $this->AgeMinimum;
    }

    public function setAgeMinimum(?int $AgeMinimum): static
    {
        $this->AgeMinimum = $AgeMinimum;

        return $this;
    }

    public function isCoupDeCoeur(): ?bool
    {
        return $this->coupDeCoeur;
    }

    public function setCoupDeCoeur(bool $coupDeCoeur): static
    {
        $this->coupDeCoeur = $coupDeCoeur;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getAffichage()
    {
        return $this->affichage;
    }

    public function setAffichage($affichage): static
    {
        $this->affichage = $affichage;

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
            $seance->setFilm($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): static
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getFilm() === $this) {
                $seance->setFilm(null);
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
            $diffusion->setFilms($this);
        }

        return $this;
    }

    public function removeDiffusion(Diffusion $diffusion): static
    {
        if ($this->diffusions->removeElement($diffusion)) {
            // set the owning side to null (unless already changed)
            if ($diffusion->getFilms() === $this) {
                $diffusion->setFilms(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setFilm($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getFilm() === $this) {
                $avi->setFilm(null);
            }
        }

        return $this;
    }


}
