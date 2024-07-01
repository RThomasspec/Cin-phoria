<?php

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('salle', EntityType::class, [
            'class' => Salle::class,
            'choice_label' => 'nom',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->orderBy('s.nom', 'ASC');
            },
        ])
        ->add('horaire', ChoiceType::class, [
            'choices' => $options['horaire_choices'],
            'placeholder' => 'Sélectionner un horaire',
        ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
