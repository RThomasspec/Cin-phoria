<?php


namespace App\Tests;

use App\Entity\Diffusion;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\HoraireRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\SalleRepository;
use App\Entity\Film;
use App\Controller\MenuController;
use App\Entity\Horaire;
use App\Entity\Cinema;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Repository\CinemaRepository;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmTest extends WebTestCase

{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testEntitéesCréationSeance()
    
    {

        $cinema = new Cinema();

        $cinema->setNom('Cinéma XYZ');
        $cinema->setAdresse('12345 Rue de la République');
        $cinema->setGSM('04 94 00 00 00');

        $this->entityManager->persist($cinema);
        $this->entityManager->flush();

        // Arrange: Configurer l'environnement de test

        // Vous devez créer un objet Seance

        $film = new Film();
        // Act
        $film->setTitre('Inception');
        $film->setDescription('A mind-bending thriller');
        $film->setAgeMinimum(12);
        $film->setCoupDeCoeur(true);
        $film->setGenre('Drame');
        $film->setIdImage(uniqid() . "png");
        $film->setAffichage('../IMG/trap.jpeg');
        // Act: Ajouter une séance au film
        $this->entityManager->persist($film);
        $this->entityManager->flush();

        $diffusion = new Diffusion();

        $diffusion->setCinemas($cinema);
        $diffusion->setFilms($film);

        $this->entityManager->persist($diffusion);
        $this->entityManager->flush();

        $salle = new Salle();

        $salle->setNom('Salle 7');
        $salle->setNbPlaces(20);
        $salle->setCinema($cinema);
        $salle->setQualite('STANDARD');

        $this->entityManager->persist($salle);
        $this->entityManager->flush();

        $horaire = new Horaire();

        $horaire->setJour('Lundi');
        $horaire->setSalle($salle);
        $horaire->setDebut(new \DateTime('2022-01-15 09:00:00'));
        $horaire->setFin(new \DateTime('2022-01-15 10:00:00'));
        
        $this->entityManager->persist($horaire);
        $this->entityManager->flush();

            $seance = new Seance();
            $seance = $seance->setFilm($film);
            $seance = $seance->setQualite("STANDARD");
            $seance = $seance->setCinema($cinema);
            $seance = $seance->setHeureDebut($horaire->getDebut());
            $seance = $seance->setHeureFin($horaire->getFin());
            $seance = $seance->setHoraire($horaire);
            $seance = $seance->setSalle($salle);
            $seance = $seance->setPrix(20);
            $seance = $seance->setPlaceDispoPMR(5);
            $seance = $seance->setPlaceDispo(20);

            $film->addSeance($seance);

            // Assert: Vérifier que la séance a été ajoutée correctement
            $this->assertTrue($film->getSeances()->contains($seance));
            $this->assertSame($film, $seance->getFilm());

            $this->entityManager->persist($seance);
            $this->entityManager->flush();
            restore_exception_handler();

    }


}
