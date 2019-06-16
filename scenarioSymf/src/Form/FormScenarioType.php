<?php

namespace App\Form;

use App\Entity\Scenario;
use App\Entity\Univers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FormScenarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('texte')
            ->add('image', FileType::class,
                    ['label'=>'Image (JPG file)'])
            ->add('univers', EntityType::class, [
                'class'=> Univers::class,
                'choice_label'=> 'nom'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
