<?php

namespace App\Service;

use App\Entity\Film;
use App\Entity\Diffusion;
use App\Entity\Cinema;
use App\Entity\Seance;
use App\Repository\CinemaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\HoraireRepository;
use App\Repository\SalleRepository;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class FilmService
{
    private $manager;
    private $imageDirectory;

    public function __construct(EntityManagerInterface $manager, string $imageDirectory)
    {
        $this->manager = $manager;
        $this->imageDirectory = $imageDirectory;
    }

    public function handleFilmForm(Film $film, CinemaRepository $cinemaRepository,  Request $request, HoraireRepository $horaireRepository, SalleRepository $salleRepository)
    {
        // Gestion de l'image
        $file = $request->files->get('form')['affichage'];
        if ($file) {
            $newFilename = $this->handleImageUpload($file);
            $film->setIdImage($newFilename);
        }

        $this->manager->persist($film);
        $this->manager->flush();

        // Gérer la diffusion
        $cinemaId = $request->request->get('form')['cinema'];
        $cinema = $cinemaRepository->find($cinemaId);
        $this->createDiffusion($film, $cinema);

        // Gérer les séances
        $formData = $request->request->all();
        $horairesSelectionnes = $formData['form']['horaires'];
        $this->createSeances($film, $horairesSelectionnes, $request, $horaireRepository, $salleRepository , $cinema);

        return $film;
    }

    private function handleImageUpload($file): string
    {
        $imagine = new Imagine();
        $image = $imagine->open($file);
        $size = new Box(150, 200); // Taille cible
        $image->resize($size);

        $newFilename = uniqid() . '.' . $file->guessExtension();
        try {
            $image->save($this->imageDirectory . '/' . $newFilename, [
                'format' => $file->guessExtension(),
                'quality' => 90,
            ]);
        } catch (FileException $e) {
            throw new \RuntimeException('Erreur lors du téléchargement de l\'image.');
        }

        return $newFilename;
    }

    private function createDiffusion(Film $film, Cinema $cinema)
    {
        $diffusion = new Diffusion();
        $diffusion->setCinemas($cinema);
        $diffusion->setFilms($film);

        $this->manager->persist($diffusion);
        $this->manager->flush();
    }

    private function createSeances(
        Film $film,
        array $horairesSelectionnes,
        Request $request,
        HoraireRepository $horaireRepository,
        SalleRepository $salleRepository,
        Cinema $cinema
    ) {
        $formData = $request->request->get('form');
        $salle = $formData['salles'];
        $prix = $formData['prix'];
        $PlaceDispoPMR = 5;

        foreach ($horairesSelectionnes as $horaireId) {
            $horaire = $horaireRepository->find($horaireId);
            $salleEntity = $salleRepository->find($salle);

            $seance = new Seance();
            $seance->setFilm($film)
                ->setQualite($salleEntity->getQualite())
                ->setCinema($cinema)
                ->setHoraire($horaire)
                ->setHeureDebut($horaire->getDebut())
                ->setHeureFin($horaire->getFin())
                ->setSalle($salleEntity)
                ->setPrix($prix)
                ->setPlaceDispoPMR($PlaceDispoPMR)
                ->setPlaceDispo($salleEntity->getNbPlaces());

            $this->manager->persist($seance);
        }

        $this->manager->flush();
    }
}
