<?php
// src/Service/CinemaService.php

namespace App\Service;
use App\Entity\Reservation;
use App\Entity\Commande;
use App\Repository\ReservationRepository;


class ReservationServiceAPI
{

    private ReservationRepository $reservationRepository;
    public function __construct(ReservationRepository $reservationRepository,)
    {
        $this->reservationRepository = $reservationRepository;
    }
    public function getReservationDetailsByUser(int $utilisateurId): array
    {
        $reservations = $this->reservationRepository->findReservationByUtilisateur($utilisateurId);

        $reservationDetails = [];
        foreach ($reservations as $reservation) {
            $reservationDetails[] = [
                'idReservation' => $reservation->getId(),
                'film' => $reservation->getSeance()->getFilm()->getTitre(),
                'idImage' => $reservation->getSeance()->getFilm()->getIdImage(),
                'jour' => $reservation->getSeance()->getHoraire()->getJour(),
                'salle' => $reservation->getSeance()->getSalle()->getNom(),
                'debut' => $reservation->getSeance()->getHoraire()->getDebut()->format('H:i'),
                'fin' => $reservation->getSeance()->getHoraire()->getFin()->format('H:i'),
                'nbPlacesReserve' => $reservation->getNbSieges()
            ];
        }

        return $reservationDetails;
    }
}