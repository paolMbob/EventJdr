<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AuthentificationAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/Inscription", name="inscription")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AuthentificationAuthenticator $authenticator, \Swift_Mailer $mailer): Response
    {
        //création du nouvel objet user
        $user = new User();

        //création du nouveau formulaire de l'objet user
        $form = $this->createForm(RegistrationFormType::class, $user);

        /***************enregistrment de la réponse*******************/
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                    // $user->getPassword()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            //préparation à l'envoi de mail
            $messageEmail = (new \Swift_Message('Activation de votre compte'))
                ->setFrom('admin@noreply.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/inscription.html.twig',
                        ['name'=> $user->getPseudo(),
                        'url'=> 'http://localhost:8000/accueil']),
                        'text/html'
                    );

            //envoi du mail
            $mailer->send($messageEmail);

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );




        }

        return $this->render('registration/inscription.html.twig', [
            'inscriptionForm' => $form->createView(),
        ]);
    }
}
