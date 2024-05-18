<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Film;
use App\Form\FilmType;

class MenuController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('base.html.twig',[
            'message' => 'Welcome to your new controller!'
        ]);
    }


    #[Route('/film/new', name: 'film_create')]
    #[Route('/film/{id}/edit', name: 'film_edit')]

    // grace a film en parametre symfony va me donner un Film grace à l'id recu en paramtre
    // dans mon cas j'ai deux route alors pour la route sans id, il ne pourra pas me récupérer mon film et ce n'est pas ce que je veux
    // il va donc falloir que je dise en paramtre que l'article peut etre null et si il est null on viens l'intancier pour qu'il soit vide
    // mais si je n'ai pas d'article via son id je veux une véritable instance de mon film d'ou la condition
    public function formFilm(Film $film = null, Request $request, ObjectManager $manager)
    {
        if(!$film){
            $film = new Film();
        }

        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($film);
            $manager->flush();
        }

        return $this->render('home/createFilm.html.twig',[
            'formFilm' => $form->createView(),
            'editMode'=> $film->getId() !== null
        ]);
    }

}
