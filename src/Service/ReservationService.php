<?php
// src/Service/CinemaService.php

namespace App\Service;
use App\Entity\Reservation;
use App\Entity\Commande;


class ReservationService
{

    public function __construct()
    {

    }

    public function createReservation($formData, $form, $manager, $seanceRepository, $user)
    {
        $reservation = new Reservation();
        $commande = new Commande();

        // Traitement des données de réservation et création de la commande
        $commande->setUtilisateur($user)->setStatut("Confirmé");
        $manager->persist($commande);
        $manager->flush();

        // Gestion des places PMR et normales
        $nbPlacesPMR = $formData['dataContentPMR'] ?? null; 
        $nbPlaces = $formData['dataContentPlace'] ?? null; 
        $prix = $formData['dataContentPrix'] ?? null; 

        $nbSieges = 0;
        if($nbPlacesPMR >= 1 && $nbPlaces == 0){
            $seanceIdPMR = $form->get('NbPlacesPMR')->getData();
            $seance = $seanceRepository->find($seanceIdPMR);
            $nbSieges = $nbPlacesPMR;
            $nbPlacesDispoPMR = $seance->getPlaceDispoPMR() - $nbPlacesPMR;
            $seance->setPlaceDispoPMR($nbPlacesDispoPMR);
        } else if($nbPlaces >= 1 && $nbPlacesPMR == 0){
            $seanceId = $form->get('NbPlaces')->getData();
            $nbSieges = $nbPlaces ;
            $seance = $seanceRepository->find($seanceId);
            $nbPlacesDispo = $seance->getPlaceDispo() - $nbPlaces;
            $seance->setPlaceDispo($nbPlacesDispo);
        } else {
            // Gestion des deux types de places
            $nbSieges = $nbPlaces + $nbPlacesPMR;
            $seanceId = $formData['reservation']['NbPlaces'];
            $seance = $seanceRepository->find($seanceId);

            // Mise à jour des places PMR et normales
            $nbPlacesDispoPMR = $seance->getPlaceDispoPMR() - $nbPlacesPMR;
            $seance->setPlaceDispoPMR($nbPlacesDispoPMR);

            $nbPlacesDispo = $seance->getPlaceDispo() - $nbPlaces;
            $seance->setPlaceDispo($nbPlacesDispo);
        }

        // Création et sauvegarde de la réservation
        $reservation->setCommande($commande)
                    ->setUtilisateur($user)
                    ->setSeance($seance)    
                    ->setNbSieges($nbSieges)
                    ->setPrix($prix)
                    ->setStatut("Confirmée");

        $manager->persist($reservation);
        $manager->flush();
        $manager->persist($seance);
        $manager->flush();

        return $reservation;
    }

}

