<?php

namespace App\Form;

use App\Entity\Univers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Modele\UniversOption;

class UniversOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choix =[];

        //parcoure notre liste d'univers de la collection univers pour setter le nom et l'id
        foreach($options['liste'] as $opt){
            $choix[$opt->getNom()] = $opt->getId();
        }

        $builder
            ->add('univers', ChoiceType::class, [
                'choices'=> $choix,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UniversOption::class,
        ]);
        $resolver->setRequired('liste');
    }
}
