<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Cinema;
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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
            ->add('coupDeCoeur', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Coup de coeur',
                'required' => false
            ])
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Note'
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
                'choice_label' => 'name',
                'mapped' => false // Important pour indiquer que ce champ ne fait pas partie de l'entité Film
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
