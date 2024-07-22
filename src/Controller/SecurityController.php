<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Utilisateur;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use App\Form\RegistrationType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Film;
use App\Form\RegistrationEmployeType;
use App\Repository\FilmRepository;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, RouterInterface $router): Response
    {
        if ($this->getUser()) {
            $refererUrl = $request->headers->get('referer');

            // Analyser l'URL pour obtenir le chemin
            $pathInfo = parse_url($refererUrl, PHP_URL_PATH);
        
            // Faire correspondre le chemin à une route
            $routeInfo = $router->match($pathInfo);
        
            // Obtenir le nom de la route
            $refererRoute = $routeInfo['_route'];
        
            // Obtenir les paramètres de la route
            $routeParams = $routeInfo;
            if(isset($routeParams['id'])){
            $id = $routeParams['id'];
            }
            unset($routeParams['_route']);

        if($routeParams){
           var_dump(("connecté et contient le routeparam"));
            if ($refererRoute == "film_reservation") {
                // Supprimer la clé de session une fois utilisée
                var_dump(("connecté et contient efererRoute == film_reservation"));
                // Rediriger l'utilisateur vers l'URL enregistrée après la connexion['id' => $film->getId()]
               return $this->redirectToRoute('film_reservation', ['id' => $id ]);
            }
        }else{
             
                // Rediriger l'utilisateur vers une autre page s'il est déjà connecté
                return $this->redirectToRoute('home'); // Remplacez 'dashboard' par la route souhaitée
            
        }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();




        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/inscription/employe', name: 'security_registration_employe')]
    public function registrationEmploye(Request $request, ObjectManager $manager, UserPasswordHasherInterface $encoder ): Response
    {

        $user = new Utilisateur();

        $form = $this->createForm(RegistrationEmployeType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // mon form sera remplis lors de la request
            // Liste des statut : 
            // ROLE_USER : Le rôle de base pour tous les utilisateurs.
            // ROLE_ADMIN : Le rôle pour les administrateurs.
            // ROLE_EMPLOYE : Le rôle pour les employés.
            // ROLE_CLIENT : Le rôle pour les clients.
            $user->setRoles(["ROLE_EMPLOYE"]);
            $user->setPrenom("EMPLOYE");
            $user->setNom("EMPLOYE");
            $hash = $encoder->hashPassword($user, $user->getpassword());
            $user->setpassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('intranet');
        }

        return $this->render('security/registrationEmploye.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request, ObjectManager $manager, UserPasswordHasherInterface $encoder ): Response
    {

        $user = new Utilisateur();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // mon form sera remplis lors de la request
            // Liste des statut : 
            // ROLE_USER : Le rôle de base pour tous les utilisateurs.
            // ROLE_ADMIN : Le rôle pour les administrateurs.
            // ROLE_EMPLOYEE : Le rôle pour les employés.
            // ROLE_CLIENT : Le rôle pour les clients.
            $user->setRoles(["ROLE_USER"]);
            $hash = $encoder->hashPassword($user, $user->getpassword());
            $user->setpassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
