<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Reservation;
use App\Entity\Seance;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Cinema;
use App\Entity\Horaire;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\StringType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('seance', ChoiceType::class, [
                'choices' => [], // Empty choices, will be populated with JavaScript
                 'expanded' => false,
                 'multiple' => false,
                 'label' => 'seance',
                 "mapped" => false,
              
             ])
           
            ->add('Prix', TextType::class, [
                'label' => 'Prix',
                'required' => true,
                'attr' => [
                    'readonly' => true, // This makes the field non-editable
                    'class' => 'form-control'],
                ])

             ->add('PersonneAMobiliteReduite', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Personne à mobilité réduite',
                'required' => true,
                "mapped" => false,
            ])

            ->add('NbPlacesPMR', ChoiceType::class, [
                'choices' => [],
                'expanded' => true,
                 'multiple' => true,
                'label' => 'Nombre de places PMR',
                "mapped" => false,
                'required' => true,
            ])
            ->add('NbPlaces', ChoiceType::class, [
                'choices' => [],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Nombre de places',
                "mapped" => false,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
