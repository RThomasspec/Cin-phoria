<?php

namespace App\Controller;

use App\Entity\Cinema;
use App\Entity\Diffusion;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use App\Entity\Film;
use App\Entity\Seance;
use App\Form\FilmType;
use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\HoraireRepository;
use App\Repository\SalleRepository;

class MenuController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(FilmRepository $repo, CinemaRepository $cinemaRepository)
    {   
        $films = $repo->findAll();
        $cinemas = $cinemaRepository->findAll();

        return $this->render('base.html.twig', [
            'films' => $films,
            'cinemas' => $cinemas
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
       // #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the admin dashboard.')]

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

            $salle = $form->get('salles')->getData();
            $horaire = $form->get('horaires')->getData();

            $seance = new Seance();
            $seance = $seance->setFilm(($film));
            $seance = $seance->setQualite($salle->getQualite());
            $seance = $seance->setCinema($cinema);
            $seance = $seance->setHeureDebut($horaire->getDebut());
            $seance = $seance->setHeureFin(($horaire->getFin()));
            $seance = $seance->setHoraire($horaireRepository->find($horaire->getId()));
            $seance = $seance->setSalle($salleRepository->find($salle->getId()));

            $manager->persist($seance);
            $manager->flush();


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

    #[Route('/filmshow/{id}', name: 'film_show')]
    public function filmShow(Film $film)
    {   
        return $this->render('home/showFilm.html.twig', [
            'film' => $film
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
