<?php

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbPlaces', NumberType::class, [
                'label' => 'Nombre de places',
                'html5' => true,
                'attr' => [
                    'min' => 0, // Optionnel, vous pouvez définir une valeur minimale
                    'step' => 1 // Optionnel, vous pouvez définir le pas de l'incrémentation
                ],
            ])
            ->add('Qualite', ChoiceType::class, [
                'choices' => [
                    '4DX' => "4DX",
                    'STANDARD' => "STANDARD",
                    '3D' => "3D",
                    '4K' => "4K"
                ],
                
                'attr' => ['class' => 'form-select'],
                'label' => 'Qualité'])
                
            ->add('nom' ,TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nomde la salle'
            ])
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,
                'placeholder' => 'Choisissez un cinéma',
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
