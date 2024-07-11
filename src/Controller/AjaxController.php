<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Cinema;
use App\Repository\CinemaRepository;
use App\Repository\HoraireRepository;
use App\Repository\SalleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Json;

class AjaxController extends AbstractController
{
    #[Route('/ajax/get-salles', name: 'ajax_get_salles', methods: ['POST'])]
    public function getSalles(Request $request, CinemaRepository $cinemaRepository, SalleRepository $salleRepository, HoraireRepository $horaireRepository)
    {
        $data = json_decode($request->getContent(), true); //Récup l'id du cinéma

        $cinemaId = $data['cinema_id'];
        $cinema = $cinemaRepository->find($cinemaId);


        $sallesArayDisponible = [];
        foreach ($cinema->getSalles() as $salle) {
                $sallesArayDisponible[] = [
                    'id' => $salle->getId(),
                    'nom' => $salle->getNom(),
                    'Qualite' => $salle->getQualite(),
                    'places' => $salle->getNbPlaces()
                ];
    
        }

        return new JsonResponse([
            'sallesAray' => $sallesArayDisponible,
        ]);
    }

    #[Route('/ajax/get-seances', name: 'ajax_get_seances', methods: ['POST'])]

    public function getSeances(Request $request, SalleRepository $salleRepository)
    {
        // chanegr le no mdu data pour qu'il ne soit pas le meme que le controlleur
        $dataSeance = json_decode($request->getContent(), true);
        $salleId = $dataSeance['salle_id'];
        $salle = $salleRepository->find($salleId);
        $horairesArayDisponible = [];
        $horairesUtilise = [];

        foreach($salle->getHoraires() as $horaire){
            
            if($horaire->getSeance() != null){
                  $horairesUtilise[] = [
                    'id' => $horaire->getId(),
                    'jour' => $horaire->getJour(),
                    'debut' => $horaire->getDebut()->format('H:i'),
                    'fin' => $horaire->getFin()->format('H:i')
                ];
            }else{
                $horairesArayDisponible[] = [
                    'id' => $horaire->getId(),
                    'jour' => $horaire->getJour(),
                    'debut' => $horaire->getDebut()->format('H:i'),
                    'fin' => $horaire->getFin()->format('H:i')
         
                ];

            }

        }

        return new JsonResponse([
            'horairesArray' => $horairesArayDisponible,
            'horairesUtilise' => $horairesUtilise
        ]);


    }

    #[Route('/ajax/get-seances-by-film', name: 'ajax_get_seances_by_film', methods: ['POST'])]

    public function getSeancesByFilm(Request $request, CinemaRepository $cinemaRepository, HoraireRepository $horaireRepository)
    {
        $data = json_decode($request->getContent(), true); 

        $filmId = $data['film_Id'];
        $horaires = $horaireRepository->findHoraireBySeance($filmId);

   
        $horaireArraySeance = [];
        foreach ($horaires as $horaire) {
                $horaireArraySeance[] = [
                    'id' => $horaire->getId(),
                    'jour' => $horaire->getJour(),
                    'debut' => $horaire->getDebut()->format('H:i'),
                    'fin' => $horaire->getFin()->format('H:i'),
                    
                ];
    
        }

        return new JsonResponse([
        
            'horaireArraySeance' => $horaireArraySeance
        ]);
    }

}
