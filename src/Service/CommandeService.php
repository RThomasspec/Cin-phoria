<?php
namespace App\Service;

use App\Repository\AvisRepository;
use App\Repository\HoraireRepository;
use App\Repository\ReservationRepository;
use App\Repository\SeanceRepository;

class CommandeService
{
    private ReservationRepository $reservationRepository;
    private SeanceRepository $seanceRepository;
    private HoraireRepository $horaireRepository;
    private AvisRepository $avisRepository;

    public function __construct(
        ReservationRepository $reservationRepository,
        SeanceRepository $seanceRepository,
        HoraireRepository $horaireRepository,
        AvisRepository $avisRepository
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->seanceRepository = $seanceRepository;
        $this->horaireRepository = $horaireRepository;
        $this->avisRepository = $avisRepository;
    }

    /**
     * Prépare les données des réservations et séances pour l'utilisateur.
     */
    public function getSeanceReservations(int $userId): array
    {
        $reservations = $this->reservationRepository->findReservationByUtilisateur($userId);

        $seanceReservations = [];

        foreach ($reservations as $reservation) {
            $seance = $this->seanceRepository->find($reservation->getSeance()->getId());
            $horaires = $this->horaireRepository->findHoraireBySeance($seance->getId());
            $horaire = $this->horaireRepository->find($horaires[0]->getId());

            $avisExists = $this->avisRepository->FilmGetAvis(
                $seance->getFilm()->getId(),
                $reservation->getUtilisateur()->getId()
            );

            $avisAlreadyGive = $avisExists <= 0;

            $seanceReservations[] = [
                'nbSieges' => $reservation->getNbSieges(),
                'prix' => $reservation->getPrix(),
                'statut' => $reservation->getStatut(),
                'titre' => $seance->getFilm()->getTitre(),
                'filmId' => $seance->getFilm()->getId(),
                'utilisateurId' => $reservation->getUtilisateur()->getId(),
                'jour' => $horaire->getJour(),
                'avisAlreadyGive' => $avisAlreadyGive,
                'debut' => $seance->getHeureDebut()->format('H:i'),
                'fin' => $seance->getHeureFin()->format('H:i'),
            ];
        }

        return $seanceReservations;
    }
}
