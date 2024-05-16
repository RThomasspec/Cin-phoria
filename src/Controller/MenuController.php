<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;


class MenuController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('home.html.twig',[
            'message' => 'Welcome to your new controller!'
        ]);
    }
}
