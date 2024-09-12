<?php

namespace App\Controller;

use App\Entity\Installations;
use App\Service\CinemaService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCode\Builder\Builder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Utilisateur;
use App\Repository\CinemaRepository;
use App\Repository\InstallationsRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PasswordCredentialsPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class APIMobilController extends AbstractController
{
    #[Route('/api/reservations', name: 'list_reservation', methods: ['POST'])]

    public function getSeanceByHoraire(Request $request, ReservationRepository $reservationRepo)
    {
        $data = json_decode($request->getContent(), true); 
        $utilisateurId = $data['utilisateur_id'];

        $reservations = $reservationRepo->findReservationByUtilisateur($utilisateurId);
         // Récupérer les informations de la séance
         $reservationDetails = [];

            for ($i = 0; $i < count($reservations); $i++) {
                $reservation = $reservations[$i];
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
    // Encoder les informations de la séance en JSON 
    $jsonReservation = json_encode($reservationDetails);

        return new JsonResponse([
            'jsonReservation' => $jsonReservation,
            'reservationDetails' => $reservationDetails
        ]);
    }

    #[Route('/api/qr-code', name: 'generate_qr_code', methods: ['POST'])]

    public function getqrcode(Request $request, ReservationRepository $reservationRepo)
    {
        $data = json_decode($request->getContent(), true); 
        $reservationId = $data['reservation_id'];

        $reservation = $reservationRepo->find($reservationId);
         // Récupérer les informations de la séance
         $reservationDetails = [];

         $reservationDetails[] = [
            'film' => $reservation->getSeance()->getFilm()->getTitre(),
            'jour' => $reservation->getSeance()->getHoraire()->getJour(),
            'salle' => $reservation->getSeance()->getSalle()->getNom(),
            'debut' => $reservation->getSeance()->getHoraire()->getDebut()->format('H:i'),
            'fin' => $reservation->getSeance()->getHoraire()->getFin()->format('H:i'),
            'nbPlacesReserve' => $reservation->getNbSieges()
        ];
    

        return new JsonResponse([
            'reservation_details' => $reservationDetails,
        ]);
    }




    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function registerAPI(Request $request,UtilisateurRepository $utilisateurRepository,UserPasswordHasherInterface $encoder ,ValidatorInterface $validator, ObjectManager $manager): JsonResponse
    {
        $user = new Utilisateur();
        $dataRegister = json_decode($request->getContent(), true);
        $password =  $dataRegister['password'];
        $email = $dataRegister['email'];

            // Règles de validation pour un mot de passe fort
    $constraint = new Assert\Length(['min' => 8]);
    $constraint = new Assert\Regex('/[A-Z]/', 'Doit contenir au moins une majuscule');
    $constraint = new Assert\Regex('/[a-z]/', 'Doit contenir au moins une minuscule');
    $constraint = new Assert\Regex('/[0-9]/', 'Doit contenir au moins un chiffre');
    $constraint = new Assert\Regex('/[!@#\$&*~]/', 'Doit contenir au moins un caractère spécial');

    $violations = $validator->validate($password, $constraint);

    if (count($violations) > 0) {
        return new JsonResponse(['error' => 'Password is not strong enough'], 400);
    }
            // Validation adresse mail
    $emailConstraint = new Assert\Email([
        'message' => 'The email "{{ value }}" is not a valid email.',
    ]);

    $violations = $validator->validate($email, $emailConstraint);

    if (count($violations) > 0) {
        return new JsonResponse(['error' => 'Invalid email address'], 400);
    }

        $user->setNom($dataRegister['nom']);

        $user->setPrenom($dataRegister['prenom']);
        $user->setMail($email);

        $isMailUsed = $utilisateurRepository->isMailUsed($email);

        if ($isMailUsed  > 0) {
            return new JsonResponse(['error' => 'Cet Email est déjà utilisé'], 400);
        }
      
        $user->setRoles(["ROLE_CLIENT"]);
        $hash = $encoder->hashPassword($user, $password);
        $user->setpassword($hash);
 
        $manager->persist($user);
        $manager->flush();

        return new JsonResponse(
            ['status' => 'Votre compte a été crée, félicitation !']
        , 201);
    }


    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginAPI( Request $request, 
    UserPasswordHasherInterface $encoder, 
    UtilisateurRepository $utilisateurRepository, AuthenticationUtils $authenticationUtils, JWTTokenManagerInterface $jwtManager): JsonResponse
{
    $dataLogin = json_decode($request->getContent(), true);
    $email = $dataLogin['email'] ?? '';
    $password = $dataLogin['password'] ?? '';

    

    $user = $utilisateurRepository->findUserByEmail($email);
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

    if (!$encoder->isPasswordValid($user, $password)) {
        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }

    $token = $this->$jwtManager->create($user);
    // Vérifier les identifiants ici et retourner un token si valide
    // ...
    return new JsonResponse([
        'message' => 'Login successful',
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getMail(),
            'token' => $token
            // Ajoutez d'autres informations utilisateur si nécessaire
        ],
    ]);


}






#[Route('/api/login/employe', name: 'api_login_employe', methods: ['POST'])]
public function loginAPIEmploye( Request $request, 
UserPasswordHasherInterface $encoder, 
UtilisateurRepository $utilisateurRepository, AuthenticationUtils $authenticationUtils, JWTTokenManagerInterface $jwtManager): JsonResponse
{
$dataLogin = json_decode($request->getContent(), true);
$email = $dataLogin['email'] ?? '';
$password = $dataLogin['password'] ?? '';



$user = $utilisateurRepository->findUserByEmail($email);
    if (!$user) {
        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }

if (!$encoder->isPasswordValid($user, $password)) {
    return new JsonResponse(['error' => 'Invalid credentials'], 401);
}

$token = $this->$jwtManager->create($user);
// Vérifier les identifiants ici et retourner un token si valide
// ...
return new JsonResponse([
    'message' => 'Login successful',
    'user' => [
        'id' => $user->getId(),
        'email' => $user->getMail(),
        'token' => $token
        // Ajoutez d'autres informations utilisateur si nécessaire
    ],
]);


}






#[Route('/api/cinema', name: 'api_login_cinema', methods: ['GET'])]
public function APICinema(CinemaRepository $cinemaRepository): JsonResponse
{

    
    $cinemas = $cinemaRepository->findAll();

    $cinemasArray = [];
    foreach ($cinemas as $cinema) {
        $cinemasArray[] = [
            'id' => $cinema->getId(),
            'name' => $cinema->getNom(),
        ];
    }

// Vérifier les identifiants ici et retourner un token si valide
// ...
return new JsonResponse([
    'cinemasArray' => $cinemasArray,
   
]);


}



#[Route('/api/salle', name: 'api_login_salle', methods: ['POST'])]
public function APISalle(Request $request,SalleRepository $salleRepository): JsonResponse
{

    $dataCinema = json_decode($request->getContent(), true);
    $salles = $salleRepository->findsallesBycinema($dataCinema['cinema_id']);

    $SallesArray = [];
    foreach ($salles as $salle) {
        $SallesArray[] = [
            'id' => $salle->getId(),
            'name' => $salle->getNom(),
        ];
    }

// Vérifier les identifiants ici et retourner un token si valide
// ...
return new JsonResponse([
    'SallesArray' => $SallesArray,
   
]);


}


#[Route('/api/installations', name: 'api_login_installations', methods: ['POST'])]
public function APIInstallations(Request $request,InstallationsRepository $installationsRepository): JsonResponse
{

    $datasalle = json_decode($request->getContent(), true);
    $installations = $installationsRepository->findInstallationsBySalle($datasalle['salle_id']);

    $InstallationsArray = [];
    foreach ($installations as $installation) {
        $InstallationsArray[] = [
            'id' => $installation->getId(),
            'salle_id' => $installation->getSalle()->getId(),
            'date_signalement' => $installation->getDateSignalement(),
            'employe_id' => $installation->getEmploye()->getId(),
            'numero_siege' => $installation->getNumeroSiege(),
            'description_probleme' => $installation->getDescriptionProbleme(),
            'etat_reparation' => $installation->isEtatReparation(),

        ];
    }

// Vérifier les identifiants ici et retourner un token si valide
// ...
return new JsonResponse([
    'InstallationsArray' => $InstallationsArray,
   
]);


}



#[Route('/api/Createinstallations', name: 'api_login_Createinstallations', methods: ['POST'])]
public function APICreateinstallations(Request $request, ObjectManager $manager,SalleRepository $salleRepository, UtilisateurRepository $utilisateurRepository): JsonResponse
{
    $installation = new Installations();


    $dataInstallations= json_decode($request->getContent(), true);
    $employe = $utilisateurRepository->find($dataInstallations['employe_id']);
    $salle = $salleRepository->find($dataInstallations['salle_id']);

    $dateSignalement = ($dataInstallations['date_signalement']);
    if (is_string($dateSignalement)) {
        // Conversion de la chaîne en DateTime
        try {
            $date_signalement = new \DateTime($dateSignalement);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format");
        }
    }

    $installation = $installation->setDateSignalement($date_signalement);
    $installation = $installation->setDescriptionProbleme($dataInstallations['description_probleme']);
    $installation = $installation->setEtatReparation(0);

    $installation = $installation->setNumeroSiege($dataInstallations['numero_siege']);
    $installation = $installation->setEmploye($employe);
    $installation = $installation->setSalle($salle);

    $manager->persist($installation);
    $manager->flush();
    
return new JsonResponse([
    'message' => 'Le signalement a été enregistré. '
   
]);


}
}
