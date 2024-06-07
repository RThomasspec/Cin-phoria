<?php

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('heureFin', null, [
                'widget' => 'single_text',
            ])
            ->add('qualite')
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'id',
            ])
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,
                'choice_label' => 'id',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
