<?php

namespace App\Tests;

use App\Entity\Film;
use App\Entity\Seance;
use PHPUnit\Framework\TestCase;

class FilmTest extends TestCase
{
    public function testAddSeance()
    {
        // Arrange: Configurer l'environnement de test
        $film = new Film();
        $seance = new Seance(); // Vous devez créer un objet Seance
        // Act
        $film->setTitre('Inception');
        $film->setDescription('A mind-bending thriller');
        $film->setAgeMinimum(12);
        $film->setCoupDeCoeur(true);
        $film->setGenre('Drame');
        $film->setAffichage('../IMG/trap.jpeg');
        // Act: Ajouter une séance au film
        $film->addSeance($seance);

        // Assert: Vérifier que la séance a été ajoutée correctement
        $this->assertTrue($film->getSeances()->contains($seance));
        $this->assertSame($film, $seance->getFilm());
    }

    public function testRemoveSeance()
    {
        // Arrange
        $film = new Film();
        $seance = new Seance(); // Créez un objet Seance
        $film->addSeance($seance);

        // Act: Supprimer la séance du film
        $film->removeSeance($seance);

        // Assert: Vérifier que la séance a été supprimée correctement
        $this->assertFalse($film->getSeances()->contains($seance));
        $this->assertNull($seance->getFilm());
    }

    public function testSettersAndGetters()
    {
        // Arrange
        $film = new Film();

        // Act
    
        $film->setTitre('Inception');
        $film->setDescription('A mind-bending thriller');
        $film->setAgeMinimum(12);
        $film->setCoupDeCoeur(true);
        $film->setGenre('Drame');
        $film->setAffichage('../IMG/trap.jpeg');

        // Assert
        $this->assertEquals('Inception', $film->getTitre());
        $this->assertEquals('A mind-bending thriller', $film->getDescription());
        $this->assertEquals(12, $film->getAgeMinimum());
        $this->assertTrue($film->isCoupDeCoeur());
        $this->assertEquals('Drame', $film->getGenre());
    }
}
