<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Entity\Diffusion;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use App\Entity\Film;
use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Form\FilmType;
use App\Form\ReservationType;
use App\Form\SalleType;
use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\HoraireRepository;
use App\Repository\SalleRepository;
use App\Repository\SeanceRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Commande;
use App\Form\ContactType;
use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use App\Form\FilmFilterType;

class MenuController extends AbstractController

{




    #[Route('/', name: 'home')]
    public function home(FilmRepository $filmRepository, CinemaRepository $cinemaRepository, Request $request)
    {   
        $cinema = new Cinema();
        $films = $filmRepository->findAll();
        $cinemas = $cinemaRepository->findAll();

        $formFilter = $this->createForm(FilmFilterType::class);
        $formFilter->handleRequest($request);
        $filmsFilter = [];
        
        if ($formFilter->isSubmitted() && $formFilter->isValid()) {


  $cinema = $formFilter->get('cinema')->getData();
  if($cinema){
    $cinemaId = $cinema->getId();
  }else{
    $cinemaId = null;
  }



            $films = $filmRepository->findByFilters(
                $cinemaId,
                $formFilter->get('genre')->getData(),
                $formFilter->get('jour')->getData()
            );
        }


        return $this->render('base.html.twig', [
            'films' => $films,
            'filmsFilter' => $filmsFilter,
            'formFilter' => $formFilter->createView(),
            'cinemas' => $cinemas
        ]);
    }

    #[Route('/intranet/ModifyOrDelteFilm', name: 'ModifyOrDelteFilm')]
    public function ModifyOrDelteFilm(FilmRepository $repo, CinemaRepository $cinemaRepository)
    {   
        $films = $repo->findAll();
        $cinemas = $cinemaRepository->findAll();

        return $this->render('home/modifyOrDeleteFilm.html.twig', [
            'films' => $films,

        ]);
    }

    #[Route('/intranet/modifyOrDeleteSalle', name: 'ModifyOrDeleteSalle')]
    public function ModifyOrDelteSalle(SalleRepository $salleRepository)
    {   
        $salles = $salleRepository->findAll();
   

        return $this->render('home/modifyOrDeleteSalle.html.twig', [
            'salles' => $salles,
        
        ]);
    }


// le isgranted est unique on ne peux mettre qu'un role mais grace à la hiérarchisation dans le security.yaml ROLE_ADMION sera
// au-dessus de ROLE_EMPLOYE ce qui permet sont accés également à ce controlleur 
    #[IsGranted('ROLE_EMPLOYE', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/film/new', name: 'film_create')]
    #[Route('/film/{id}/edit', name: 'film_edit')]

    // grace a film en parametre symfony va me donner un Film grace à l'id recu en paramtre
    // dans mon cas j'ai deux route alors pour la route sans id, il ne pourra pas me récupérer mon film et ce n'est pas ce que je veux
    // il va donc falloir que je dise en paramtre que le Film peut etre null et si il est null on viens l'intancier pour qu'il soit vide
    // mais si je n'ai pas de Film via son id je veux une véritable instance de mon film d'ou la condition
    public function formFilm(Film $film = null, Request $request, ObjectManager $manager , HoraireRepository $horaireRepository, SalleRepository $salleRepository)
    {   
     
        $imagePath = '/public/uploads/images/';

        if (!$film) {
            $film = new Film();
        }
        $form = $this->createForm(FilmType::class, $film);
            // A REPRENDRE
       // $form = $this->createForm(FilmType::class, $film, [
       //     'horaire_choices' =>$this->getHoraireChoices($horaireRepository, $film),
       // ]);
        //$imageAbsolutePath = $this->getParameter('kernel.project_dir') .$imagePath.$film->getIdImage();
        //$imageFile = new File($imageAbsolutePath);
        //$form->get('affichage')->setData($imageFile);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // La docblock @var UploadedFile $file informe l'éditeur et les outils d'analyse 
            //statique que la variable $file est de type UploadedFile.
            /** @var UploadedFile $file */
            $file = $form->get('affichage')->getData();
            $cinema = $form->get('cinema')->getData();


            if ($file) {
                //récupère le nom de fichier original sans l'extension.
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->guessExtension();
                //génère un nouveau nom de fichier unique en utilisant la fonction uniqid() et en ajoutant
                // l'extension du fichier original. Ceci évite les conflits de noms de fichiers dans le dossier de stockage.
                $newFilename = uniqid().'.'.$file->guessExtension();

                try {
                    //déplace le fichier vers ce répertoire avec le nouveau nom de fichier.
                    $file->move(
                        // récupère le chemin du répertoire de stockage des images à partir des paramètres de configuration.
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    //Si le déplacement est réussi, setIdImage($newFilename) met à jour 
                    //l'entité Film avec le nouveau nom de fichier (chemin relatif), permettant ainsi de référencer l'image stockée.
                    $film->setIdImage($newFilename);
                    $film->setAffichage(($originalFilename));
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }
            $manager->persist($film);
            $manager->flush();

            $diffusion = new Diffusion();
            $diffusion->setCinemas($cinema);
            $diffusion->setFilms($film);

            $manager->persist($diffusion);
            $manager->flush();


            $data = $form->getData();
            $formData = $request->request->all(); 
            $horairesSelectionnes = $formData['form']['horaires'];
            $PlaceDispoPMR = 5;

         //éder directement aux horaires sélectionnés

            // Vérifier les données reçues
   

            // $seances est un tableau contenant les IDs des séances sélectionnées
            foreach ($horairesSelectionnes as $horaireId) {
                $salle = $form->get('salles')->getData();
            
                $prix =  $form->get('prix')->getData();
                $seance = new Seance();
                $seance = $seance->setFilm($film);
                $seance = $seance->setQualite($salle->getQualite());
                $seance = $seance->setCinema($cinema);
                $seance = $seance->setHeureDebut($horaireRepository->find($horaireId)->getDebut());
                $seance = $seance->setHeureFin($horaireRepository->find($horaireId)->getFin());
                $seance = $seance->setHoraire($horaireRepository->find($horaireId));
                $seance = $seance->setSalle($salleRepository->find($salle->getId()));
                $seance = $seance->setPrix($prix);
                $seance = $seance->setPlaceDispoPMR($PlaceDispoPMR);
                $seance = $seance->setPlaceDispo($salle->getNbPlaces());


                $manager->persist($seance);
                $manager->flush();
            }

         

           


            return $this->redirectToRoute('film_validation', ['id' => $film->getId()]);
        }



        return $this->render('home/createFilm.html.twig', [
            'editMode' => $film->getId() !== null,
            'formFilm' => $form->createView(),
            'film' => $film
        ]);
    }

    #[Route('/filmCreation/{id}', name: 'film_validation')]
    public function filmValidation(Film $film)
    {   
        return $this->render('home/validationCreationFilm.html.twig', [
            'film' => $film
        ]);
    }


    #[Route('/filmDelete/{id}', name: 'film_delete')]
    public function filmDelete(Film $film,  FilmRepository $repo, ObjectManager $manager)
    {   

            // Vérifier si l'entité existe
            if (!$film) {
                throw $this->createNotFoundException(
                    'No film found'
                );
            }
    
            // Supprimer l'entité
            $manager->remove($film);
            $manager->flush();
    
            // Rediriger ou retourner une réponse appropriée
            return $this->redirectToRoute('intranet'); // Remplacez 'success_page' par la route de votre choix
        
  
    }


    #[Route('/salleDelete/{id}', name: 'salle_delete')]
    public function salleDelete(Salle $salle,  SalleRepository $repo, ObjectManager $manager)
    {   

            // Vérifier si l'entité existe
            if (!$salle) {
                throw $this->createNotFoundException(
                    'No salle found'
                );
            }
    
            // Supprimer l'entité
            $manager->remove($salle);
            $manager->flush();
    
            // Rediriger ou retourner une réponse appropriée
            return $this->redirectToRoute('intranet'); // Remplacez 'success_page' par la route de votre choix
        
  
    }

    #[Route('/filmshow/{id}', name: 'film_show')]
    public function filmShow(Film $film)
    {   
        return $this->render('home/showFilm.html.twig', [
            'film' => $film
        ]);
    }

    #[Route('/filmshow/reservation/{id}', name: 'film_reservation')]
    public function reservation(Request $request, TokenStorageInterface $tokenStorage,Film $film,ObjectManager $manager, SeanceRepository $seanceRepository, HoraireRepository $horaireRepository)
    {   
        

        $reservation = new Reservation();
        $commande = new Commande();
        

        $form = $this->createForm(ReservationType::class, $reservation);
      
        $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

    
            $commande = $commande->setUtilisateur($user);
            $commande = $commande->setStatut("Confirmé");
            $manager->persist($commande);
            $manager->flush();

            $formData = $request->request->all(); 

            $nbPlacesPMR = $formData['dataContentPMR'] ?? null; 
            $nbPlaces = $formData['dataContentPlace'] ?? null; 

            $prix = $formData['dataContentPrix'] ?? null; 

            $nbSieges = 0;
            if($nbPlacesPMR >= 1 && $nbPlaces == 0){
                $seanceIdPMR = $form->get('NbPlacesPMR')->getData();
                $seance = $seanceRepository->find($seanceIdPMR);
                $nbSieges = $nbPlacesPMR;
                $nbPlacesDispoPMR = $seance->getPlaceDispoPMR() - $nbPlacesPMR;
                
                $seance = $seance->setPlaceDispoPMR($nbPlacesDispoPMR);
            }else if($nbPlaces >= 1 && $nbPlacesPMR == 0){
                $seanceId = $form->get('NbPlaces')->getData();
                $nbSieges = $nbPlaces ;
                $seance = $seanceRepository->find($seanceId);
                
                $nbPlacesDispo = $seance->getPlaceDispo() - $nbPlaces;

                $seance = $seance->setPlaceDispo($nbPlacesDispo);
            }else{
                $nbSieges = $nbPlaces + $nbPlacesPMR;
                $seanceId = $formData['reservation']['NbPlaces'];
                $seance = $seanceRepository->find($seanceId);


                $nbPlacesDispoPMR = $seance->getPlaceDispoPMR() - $nbPlacesPMR;
                $seance = $seance->setPlaceDispoPMR($nbPlacesDispoPMR);

                $nbPlacesDispo = $seance->getPlaceDispo() - $nbPlaces;
                $seance = $seance->setPlaceDispo($nbPlacesDispo);
            }

            $reservation = $reservation->setCommande($commande);
            $reservation = $reservation->setUtilisateur($user);
            $reservation = $reservation->setSeance($seance);    
            $reservation = $reservation->setNbSieges($nbSieges);
            $reservation = $reservation->setPrix($prix);
            $reservation = $reservation->setStatut("Confirmée");

            $manager->persist($reservation);
            $manager->flush();

            $manager->persist($seance);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('reservation/reservation.html.twig', [
            'formReservation' => $form->createView(),
            'film' => $film,
        ]);
    }



    #[Route('/redirectionReservation', name: 'redirection_reservation')]
    public function redirectionReservation()
    {   
        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        return $this->render('reservation/reservation.html.twig', [
        
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(FilmRepository $filmRepository, ReservationRepository $reservationRepository)
    {   
        $films = $filmRepository->findAll();
        $reservationByFilm = [];
        foreach ($films as $film){
            $reservationByFilm[$film->getId()] = $reservationRepository->findReservationsByFilmId($film->getId());
        }

        return $this->render('home/dashboard.html.twig', [
            'reservationByFilm' => $reservationByFilm,
            'films' => $films
        ]);
    }

    #[IsGranted('ROLE_CLIENT', message: 'You are not allowed to access.')]
    #[Route('/monCompte/commande', name: 'commande')]
    public function commande(SeanceRepository $seanceRepository, TokenStorageInterface $tokenStorage, ReservationRepository $reservationRepository)
    {   
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
// Impossible d'obtenir l'id de l'utilisateur via son accesseur, autre moyen pour l'obtenir
        if ($user instanceof Utilisateur) {
            $reflectionClass = new \ReflectionClass($user);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $userId = $property->getValue($user);
            
        } 
        // film, date heure, nbplace, prix, staut
     
        
        $reservations = $reservationRepository->findReservationByUtilisateur($userId);
     
       

   
        $seanceReservations = [];

        for ($i = 0; $i < count($reservations); $i++) {

            $reservation = $reservations[$i];
            $seance = $seanceRepository->find($reservation->getSeance()->getId());

                $seanceReservations[] = [
                        'nbSieges' => $reservation->getNbSieges(),
                        'prix' => $reservation->getPrix(),
                        'statut' => $reservation->getStatut(),
                        'titre' => $seance->getFilm()->getTitre(),
                        'debut' => $seance->getHeureDebut()->format('H:i'),
                        'fin' => $seance->getHeureFin()->format('H:i'),
                        
                    ];
        
            }
        

        return $this->render('home/commande.html.twig', [
            'seanceReservations' => $seanceReservations,
        ]);
    }

    #[IsGranted('ROLE_CLIENT', message: 'You are not allowed to access.')]
    #[Route('/monCompte', name: 'monCompte')]
    public function Compte()
    {   
        $user = $this->getUser();

        
        return $this->render('home/monCompte.html.twig', [
            'user' => $user
        
        ]);
    }

    #[Route('/cinema/{id}', name: 'cinema_show')]
    public function cinemaShow(Cinema $cinema,FilmRepository $filmRepository)
    {
        $films = $filmRepository->findFilmsByCinema($cinema->getId());
        return $this->render('cinema/cinemashow.html.twig', [
            'films' => $films
        ]);
    }

    #[Route('/intranet', name: 'intranet')]
    public function intranet ( UtilisateurRepository $utilisateurRepository)
    {
        $userId = $this->getUser(); 
        $user = $utilisateurRepository->find($userId);
        $today = new \DateTime();
        if ($user) {
            if ($user->getRoles('ROLE_EMPLOYE') && $today->format('l') != 'Wednesday') {
                $IsCreatable = false;
            }else{
                $IsCreatable = true;
            }
    
        return $this->render('home/intranet.html.twig', [
            'IsCreatable' => $IsCreatable,
            
        ]);
    }
    }


    #[Route('/salle/new', name: 'form_salle')]
    #[Route('/salle/{id}/new', name: 'salle_edit')]
    public function formClasse (Request $request, Salle $salle = null, SalleRepository $salleRepository, ObjectManager $manager)
    {
        if(!$salle){
        $salle = new Salle();
        }

        $form = $this->createForm(SalleType::class,$salle);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($salle);
            $manager->flush();

            return $this->redirectToRoute('intranet');
        }
        return $this->render('home/createSalle.html.twig', [
            'editMode' => $salle->getId() !== null,
            'formSalle' => $form->createView()
            
        ]);
    }





    private function convertAndResizeImage($filename, $format)
    {
        $imagine = new Imagine();
        $imagePath = $this->getParameter('images_directory') . '/' . $filename;

        $image = $imagine->open($imagePath);

        // Redimensionner l'image
        $size = new Box(600, 900); // Taille cible (ajustez selon vos besoins)
        $image->resize($size);

        // Sauvegarder l'image dans le format choisi, en écrasant le fichier existant
        $image->save($imagePath, [
            'format' => $format,
            $format . '_quality' => 90 // Ajustez la qualité si nécessaire
        ]);
    }
}
