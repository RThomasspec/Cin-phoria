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

                ->add('horaire', EntityType::class, [
               'class' => Horaire::class,
                'choice_label' => 'jour',
                'placeholder' => 'Sélectionnez une salle',
                "mapped" => false
                
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
                'required' => false,
                "mapped" => false,
            ])

            ->add('NbPlacesPMR', ChoiceType::class, [
                'placeholder' => 'Sélectionnez un nombre',
                'choices' => $this->getNumberChoices(),
                'expanded' => false,
                'multiple' => false,
                'label' => 'Nombre de place',
                "mapped" => false,
                'required' => true,
            ])
            ->add('NbPlaces', ChoiceType::class, [
                'placeholder' => 'Sélectionnez un nombre',
               'choices' => $this->getNumberChoices(),
                'expanded' => false,
                'multiple' => false,
                'label' => 'Nombre de place',
                "mapped" => false,
                'required' => true,
            ])
        ;
    
    }

    private function getNumberChoices()
    {
        $choices = [];
        
        for ($i = 1; $i <= 100; $i++) {
            $choices[$i] = $i;
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
