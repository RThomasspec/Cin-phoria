<?php

namespace App\Entity;

use App\Repository\DiffusionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiffusionRepository::class)]
class Diffusion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'diffusions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cinema $cinemas = null;

    #[ORM\ManyToOne(inversedBy: 'diffusions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Film $films = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCinemas(): ?Cinema
    {
        return $this->cinemas;
    }

    public function setCinemas(?Cinema $cinemas): static
    {
        $this->cinemas = $cinemas;

        return $this;
    }

    public function getFilms(): ?Film
    {
        return $this->films;
    }

    public function setFilms(?Film $films): static
    {
        $this->films = $films;

        return $this;
    }
}
