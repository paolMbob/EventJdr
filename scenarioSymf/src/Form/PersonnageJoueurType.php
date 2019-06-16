<?php

namespace App\Form;

use App\Entity\PersonnageJoueur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TypeJoueur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PersonnageJoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('race',EntityType::class, [
                'class'=> TypeJoueur::class,
                'choice_label'=>'race'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PersonnageJoueur::class,
        ]);
    }
}
