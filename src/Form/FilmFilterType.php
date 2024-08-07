<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Cinema;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FilmFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Selectionner un Cinema',  
                "mapped" => false
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
                'placeholder' => 'Choisissez un genre',
                'required' => false
                
            ])
            ->add('jour',ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Jour',
                'choices' => [
                    'Lundi' => "Lundi",
                    'Mardi' => "Mardi",
                    'Mercredi' => "Mercredi",
                    'Jeudi' => "Jeudi",
                    'Vendredi' => "Vendredi",
                    'Samedi' => "Samedi",
                    'Dimanche' => "Dimanche"
                ],
                'required' =>false,
                'mapped' => false,
                'placeholder' => 'Choisissez un jour de la semaine',
            ])
            ->add('filtre', SubmitType::class, [
            'label' => 'Apply Filters',
        'attr' => ['class' => 'btn btn-primary']]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
