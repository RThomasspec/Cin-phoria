<?php
// src/Service/CinemaService.php
namespace App\Service;

use App\Repository\CinemaRepository;
use App\Entity\Avis;
use App\Entity\Film;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
class AvisService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function calculerMoyenneAvis($avis)
    {
        $totalNote = 0;
        $countAvis = count($avis);
        
        if ($countAvis > 0) {
            foreach ($avis as $noteAvis) {
                $totalNote += $noteAvis->getNote();
            }
            return round($totalNote / $countAvis);
        }
        
        return 0; // Si aucun avis, la note est 0
    }



        /**
     * Gère la création ou la mise à jour d'un avis pour un film.
     */
    public function saveAvis(Avis $avis, Film $film, Utilisateur $utilisateur): void
    {
        try{ 
        $this->entityManager->beginTransaction();
        $avis->setUtilisateur($utilisateur);
        $avis->setFilm($film);
        $avis->setValide(false); // L'avis n'est pas validé par défaut

        // Persiste l'avis dans la base de données
        $this->entityManager->persist($avis);
        $this->entityManager->flush();
        
        $this->entityManager->commit();

    } catch (\Exception $e) {
        // Annule la transaction en cas d'erreur
        $this->entityManager->rollback();

        // Relancer l'exception ou gérer l'erreur ici
        throw $e;
    }

    }
}

