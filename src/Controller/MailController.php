<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ContactType;

class MailController extends AbstractController
{
 
    #[Route('/contact', name: 'contact')]
    public function contact (Request $request, MailerInterface $mailer)
    {   

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            

            $email = (new Email())
            ->from($data['mail']) // Assurez-vous d'avoir un champ 'email' dans votre formulaire
            ->to('destination@example.com') // Remplacez par l'adresse e-mail où vous souhaitez envoyer les messages
            ->subject('Nouveau message de contact')
            ->text('Expéditeur: '.$data['mail'].'Objet :'.$data['objet'].'Message: '.$data['description']); // Assurez-vous d'avoir les champs 'name' et 'message' dans votre formulaire

            $mailer->send($email);

            return $this->redirectToRoute('home');
        

        }

    
        return $this->render('home/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
