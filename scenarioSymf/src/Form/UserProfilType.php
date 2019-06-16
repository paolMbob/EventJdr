<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('pseudo')
        //ajouter un choix en fonction de la modification ou non de l'évènement
            ->add('plainPassword', PasswordType::class, [
                        // instead of being set onto the object directly,
                        // this is read and encoded in the controller
                        'mapped' => false,
                        'required'=>false,
                        'constraints' => [
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ],
                    ])
            // ->add('choix',ChoiceType::class,
            //     ['choices'=>[
            //             'yes'=>true,
            //             'no'=>false
            //         ],
            //
            //     'choice_label' => function ($choice, $key, $value) {
            //         if (true === $choice) {
            //                 return 'Definitely!';
            //         }
            //     }
            // ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $choice = $event->getData();
                // $choice = $event;
                if($choice){
                    return "ok";
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
