<?php
namespace App\Service;

use App\Entity\Salle;
use App\Entity\Horaire;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;

class SalleService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Gère la création ou la mise à jour d'une salle et de ses horaires.
     */
    public function saveSalle(Salle $salle, array $jours, array $horaires): void
    {
        // Persiste la salle dans la base de données
        $this->entityManager->persist($salle);
        $this->entityManager->flush();

        // Génération des horaires associés à la salle
        foreach ($jours as $jour) {
            foreach ($horaires as $horaireData) {
                $horaire = new Horaire();
                $horaire->setJour($jour);
                $horaire->setSalle($salle);
                $horaire->setDebut(new \DateTime($horaireData[0]));
                $horaire->setFin(new \DateTime($horaireData[1]));

                $this->entityManager->persist($horaire);
            }
        }

        // Flush pour sauvegarder les horaires
        $this->entityManager->flush();
    }


    
}
