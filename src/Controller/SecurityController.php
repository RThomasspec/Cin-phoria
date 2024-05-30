<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
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
            $user->setStatut("ROLE_CLIENT");
            $hash = $encoder->hashPassword($user, $user->getpassword());
            $user->setpassword($hash);
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
