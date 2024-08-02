<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Cinema;
use App\Entity\Horaire;
use App\Entity\Seance;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Validator\Constraints\Image;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Titre du film'
            ])
            ->add('prix', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prix de la séance',
                'choices' => [
                    'Prix de la séance 20 euro' => 20,
                    'Prix de la séance  15 euro' => 15,
                    'Prix de la séance  12 euro' => 12,
                    'Prix de la séance  10 euro' => 10
                ],
                'mapped' => false
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Description'
            ])
            ->add('AgeMinimum', ChoiceType::class, [
                'choices' => [
                    '7+' => 7,
                    '12+' => 12,
                    '16+' => 16,
                    '18+' => 18
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Note'
            ])

            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Comédie' => "Comédie",
                    'Horreur' => "Horreur",
                    'Drame' => "Drame",
                    'Fantastique' => "Fantastique",
                    'Animation' => "Animation",
                    'Musical' => "Musical",
                    'Documentaire' => "Documentaire",
                    'Guerre' => "Guerre",
                    'Western' => "Western",
                    'Biopic' => "Biopic",
                    'Comédie romantique' => "Comédie romantique",
                    'Historique' => "Historique",
                    'Retransmission' => "Retransmission",
                    'Court métrage' => "Court métrage",
                    'Thriller' => "Thriller",
                    'Action / Aventure' => "Action / Aventure",
                    'Science-fiction' => "Science-fiction",
                    'Comédie dramatique' => "Comédie dramatique",
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Note'
            ])
            ->add('coupDeCoeur', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Coup de coeur',
                'required' => false
            ])
            ->add('affichage', FileType::class, [
                'label' => 'Affiche (Image file)',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024K',
                        'maxSizeMessage' => 'The maximum allowed file size is 5MB.',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF)',
                        // 'minWidth' => 100,
                        // 'maxWidth' => 2000,
                        //'minHeight' => 100,
                        //'maxHeight' => 2000,
                        //'minWidthMessage' => 'The image width is too small (minimum width is {{ min_width }}px)',
                        //'maxWidthMessage' => 'The image width is too large (maximum width is {{ max_width }}px)',
                        //'minHeightMessage' => 'The image height is too small (minimum height is {{ min_height }}px)',
                        // 'maxHeightMessage' => 'The image height is too large (maximum height is {{ max_height }}px)',
                    ]),
                ],
            ])
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un cinéma',
                "mapped" => false
                
            ])
            ->add('salles', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une salle',
                "mapped" => false
            ])

            ->add('horaires', ChoiceType::class, [
               'choices' => [], // Empty choices, will be populated with JavaScript
                'expanded' => true,
                'multiple' => true,
                'label' => 'Séances',
                "mapped" => false,
                'required' => true,
            ]);
    

           // Ajouter un écouteur d'événements pour les données dynamiques
        
            // Ajouter des options dynamiques si les données sont disponibles
       
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
