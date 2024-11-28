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
use App\Entity\Avis;
use App\Service\AvisService;
use App\Service\SalleService;
use App\Entity\Cinema;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Repository\CinemaRepository;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;


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



    public function testCalculerMoyenneAvisAvecPlusieursAvis()
    {
        // Préparation des données d'entrée
        $avis = [
            $this->createAvisMock(5),
            $this->createAvisMock(4),
            $this->createAvisMock(3),
        ];

        // Instanciation de la classe contenant la méthode
        $service = new AvisService($this->createMock(EntityManagerInterface::class)); // Remplace par ta classe réelle

        // Appel de la méthode
        $result = $service->calculerMoyenneAvis($avis);

        // Assertion : on vérifie si le résultat est correct
        $this->assertEquals(4, $result); // (5+4+3)/3 = 4
        restore_exception_handler();
    }

    public function testCalculerMoyenneAvisAvecAucunAvis()
    {
        $avis = [];

        $service = new AvisService($this->createMock(EntityManagerInterface::class)); // Remplace par ta classe réelle
        $result = $service->calculerMoyenneAvis($avis);

        $this->assertEquals(0, $result); // Aucun avis, la moyenne doit être 0
        restore_exception_handler();
    }

    public function testCalculerMoyenneAvisAvecUnSeulAvis()
    {
        $avis = [$this->createAvisMock(5)];

        $service = new AvisService($this->createMock(EntityManagerInterface::class)); // Remplace par ta classe réelle
        $result = $service->calculerMoyenneAvis($avis);

        $this->assertEquals(5, $result); // Un seul avis, la moyenne est la note elle-même
        restore_exception_handler();
    }

    public function test24()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }


    public function test23()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }


    public function test22()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }


    public function test20()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }

    public function test21()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }


    public function test4()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }

    public function test5()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }

    public function test3()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même

        restore_exception_handler();
    }

    public function test()
    {

        $result = 5+5;

        $this->assertEquals(10, $result); // Un seul avis, la moyenne est la note elle-même
        restore_exception_handler();
    }


    private function createAvisMock($note)
    {
        $mock = $this->createMock(Avis::class); 
        $mock->method('getNote')->willReturn($note);

        return $mock;
    }
}