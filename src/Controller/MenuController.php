<?php

namespace App\Controller;

use App\Entity\Avis;
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
use App\Entity\Horaire;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Commande;
use App\Form\AvisType;
use App\Form\FilmFilterType;
use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use App\Service\CinemaService;
use App\Repository\AvisRepository;
use App\Service\AvisService;
use App\Service\CommandeService;
use App\Service\FilmService;
use App\Service\ReservationService;
use App\Service\SalleService;
use PhpParser\Node\Expr\Instanceof_;
use Symfony\Component\Validator\Constraints\IsTrue;

class MenuController extends AbstractController

{

    #[Route('/', name: 'home')]
    public function home(FilmRepository $filmRepository, CinemaRepository $cinemaRepository, Request $request)
    {   
        // Récupération de tous les films et cinémas
        $films = $filmRepository->findAll();
        $cinemas = $cinemaRepository->findAll();
    
        // Filtrer les doublons par titre
        $uniqueFilms = [];
        foreach ($films as $film) {
            $title = $film->getTitre();
            if (!isset($uniqueFilms[$title])) {
                $uniqueFilms[$title] = $film;
            }
        }
    
        // Convertir les valeurs du tableau associative en array indexé
        $uniqueFilms = array_values($uniqueFilms);
    
        // Création du formulaire de filtre
        $formFilter = $this->createForm(FilmFilterType::class); 
        $formFilter->handleRequest($request);
        $filmsFilter = [];
    
        // Vérification de la soumission du formulaire
        if ($formFilter->isSubmitted() && $formFilter->isValid()) {
            // Récupération des données du formulaire
            $cinema = $formFilter->get('cinema')->getData();
            $cinemaId = $cinema ? $cinema->getId() : null;
    
            // Application des filtres
            $filmsFilter = $filmRepository->findByFilters(
                $cinemaId,
                $formFilter->get('genre')->getData(),
                $formFilter->get('jour')->getData()
            );
        }
    
        // Rendu de la vue
        return $this->render('base.html.twig', [
            // On laisse 'films' vide si on ne veut pas l'afficher
            'films' => $uniqueFilms, 
            'filmsFilter' => $filmsFilter, // Liste filtrée
            'formFilter' => $formFilter->createView(),
            'cinemas' => $cinemas, // Liste des cinémas
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
    public function formFilm(CinemaRepository $cinemaRepository,FilmService $filmService, Film $film = null, Request $request, ObjectManager $manager , HoraireRepository $horaireRepository, SalleRepository $salleRepository)
    {   
     
        $imagePath = '/public/uploads/images/';

        if (!$film) {
            $film = new Film();
        }
        $film = $film ?? new Film();
        $formFilm = $this->createForm(FilmType::class, $film);

        $formFilm->handleRequest($request);

        if ($formFilm->isSubmitted() && $formFilm->isValid()) {
            $filmService->handleFilmForm($film, $cinemaRepository,$request, $horaireRepository, $salleRepository);
            return $this->redirectToRoute('film_validation', ['id' => $film->getId()]);
        }



        return $this->render('home/createFilm.html.twig', [
            'editMode' => $film->getId() !== null,
            'formFilm' => $formFilm->createView(),
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


    #[Route('/intranet/ModifyOrDelteFilm/filmDelete/{id}', name: 'film_delete')]
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
            return $this->redirectToRoute('ModifyOrDelteFilm'); // Remplacez 'success_page' par la route de votre choix
        
  
    }


    #[Route('intranet/modifyOrDeleteSalle/salleDelete/{id}', name: 'salle_delete')]
    public function salleDelete(Salle $salle, ObjectManager $manager)
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
            return $this->redirectToRoute('ModifyOrDeleteSalle'); // Remplacez 'success_page' par la route de votre choix
        
  
    }

    #[Route('intranet/valideAvis/delete/{id}', name: 'avis_delete')]
    public function avisDelete(Avis $avis, ObjectManager $manager)
    {   

            // Vérifier si l'entité existe
            if (!$avis) {
                throw $this->createNotFoundException(
                    'No avis found'
                );
            }
    
            // Supprimer l'entité
            $manager->remove($avis);
            $manager->flush();
    
            // Rediriger ou retourner une réponse appropriée
            return $this->redirectToRoute('valideAvis'); // Remplacez 'success_page' par la route de votre choix
        
  
    }

    #[Route('intranet/valideAvis/valide/{id}', name: 'avis_valide')]
    public function avisValide(Avis $avis, ObjectManager $manager)
    {   
            

            $avis = $avis->setValide(true);
    
            // Supprimer l'entité
            $manager->persist($avis);
            $manager->flush();
    
            // Rediriger ou retourner une réponse appropriée
            return $this->redirectToRoute('valideAvis'); // Remplacez 'success_page' par la route de votre choix
        
  
    }

    #[Route('/filmshow/{id}', name: 'film_show')]
    public function filmShow(Film $film, AvisRepository $avisRepository, AvisService $avisService)
    {   
        $avis = $avisRepository->findAvisValidebyFilm($film->getId());
        $note = $avisService->calculerMoyenneAvis($avis);

        
        return $this->render('home/showFIlm.html.twig', [
            'film' => $film,
            'avis' => $avis, 
            'note' => $note
        ]);
    }

    #[Route('/filmshow/reservation/{id}', name: 'film_reservation')]
    public function reservation(Request $request,ReservationService $reservationService, TokenStorageInterface $tokenStorage, Film $film,ObjectManager $manager, SeanceRepository $seanceRepository, HoraireRepository $horaireRepository)
    {   
        

        $reservation = new Reservation();
        $commande = new Commande();
        

        $form = $this->createForm(ReservationType::class, $reservation);
      
        $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

    
            $formData = $request->request->all(); 

            $reservation = $reservationService->createReservation($formData, $form, $manager, $seanceRepository, $user);

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


    #[IsGranted('ROLE_EMPLOYE', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/intranet/valideAvis', name: 'valideAvis')]
    public function valideAvis(AvisRepository $avisRepository, ReservationRepository $reservationRepository)
    {   

          $avis = $avisRepository->findInvalidAvis();


        return $this->render('home/valideAvis.html.twig', [

            'avisListe' => $avis
            
        ]);
    }

    #[IsGranted('ROLE_CLIENT', message: 'You are not allowed to access.')]
    #[Route('/monCompte/commande', name: 'commande')]
    public function commande(CommandeService $commandeService,AvisRepository $avisRepository,HoraireRepository $horaireRepository, SeanceRepository $seanceRepository, TokenStorageInterface $tokenStorage, ReservationRepository $reservationRepository)
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
        $seanceReservations = $commandeService->getSeanceReservations($userId);

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

    #[IsGranted('ROLE_CLIENT', message: 'You are not allowed to access.')]
    #[Route('/monCompte/commande/avis/{id}/{utilisateurId}', name: 'avis')]
    public function Avis(AvisService $avisService, Film $film, int $utilisateurId, ObjectManager $manager, Avis $avis = null, Request $request, UtilisateurRepository $utilisateurRepository)
    {   
        $utilisateur = $utilisateurRepository->find($utilisateurId);
        if (!$avis) {
            $avis = new Avis();
        }

        $formAvis = $this->createForm(AvisType::class, $avis);

        $formAvis->handleRequest($request);

        if($formAvis->isSubmitted() && $formAvis->isValid()){
            
            $avisService->saveAvis($avis, $film, $utilisateur);

            return $this->redirectToRoute('commande');
        }

        
        return $this->render('home/avis.html.twig', [
            'formAvis' => $formAvis
        
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
    public function formSalle (SalleService $salleService,Request $request, Salle $salle = null, SalleRepository $salleRepository, ObjectManager $manager)
    {
        if(!$salle){
        $salle = new Salle();
        }

        $form = $this->createForm(SalleType::class,$salle);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                $horaires = [
                    ['09:00:00', '10:00:00'],
                    ['11:00:00', '12:00:00'],
                    ['13:00:00', '14:00:00'],
                    ['15:00:00', '16:00:00'],
                    ['17:00:00', '18:00:00'],
                ];
                $salleService->saveSalle($salle, $jours, $horaires);


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
