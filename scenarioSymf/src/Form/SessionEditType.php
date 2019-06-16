<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Scenario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\PersonnageJoueur;

class SessionEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut')
            ->add('lieu')
            ->add('dateFin')
            // ->add('personnageJoueur', EntityType::class, [
            //     'class'=> PersonnageJoueur::class,
            //     'choice_label'=> 'user.pseudo',
            //     'expended'=> 'checkboxes'
            // ])
            ->add('scenario',EntityType::class, [
                'class'=> Scenario::class,
                'choice_label'=>'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
