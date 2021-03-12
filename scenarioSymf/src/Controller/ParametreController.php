<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Scenario;
use App\Entity\Univers;
use App\Entity\User;
use App\Entity\Commentaire;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UniversOptionType;
use App\Modele\UniversOption;
use App\Form\FormScenarioType;
use App\Form\FormUniversType;
use App\Form\ChangeUserType;
use App\Form\FormUserType;
use App\Form\UserProfilType;




/**
*@Route("/parametre")
**/

class ParametreController extends AbstractController

{
    /**
    *@Route("/accueil", name="parametre_accueil")
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function paramAdmin()
    {
        return $this->render('home/parametre.html.twig');

    }

    /**
    *@Route("/utilisateur", name="parametre_utilisateur")
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function listeUser()
    {

        //recuperation de le collection user
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->findAll();

        //tests 2 

        return $this->render('home/parametre_utilisateur.html.twig',
                                array("users"=>$utilisateur));
    }

    /**
    *@Route("/modificationCompte/{id}", name="modificationCompte")
    **requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function modificationCompte(Request $requete, $id, UserPasswordEncoderInterface $passwordEncoder,  \Swift_Mailer $mailer)
    {

        //recuperation du user
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->find($id);

        //création du formulaire commentaire
        $formulaireUser = $this->createForm(ChangeUserType::class, $utilisateur);

        $formulaireUser->handleRequest($requete);

        if($formulaireUser->isSubmitted() && $formulaireUser->isValid()){

            // $commentaire->setArticle($article);

            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $formulaireUser->get('plainPassword')->getData()
                )
            );

            //récupération du controlleur
            $gestionnaire = $this->getDoctrine()->getManager();


            //enregistre l'ajout des données
            $gestionnaire->flush();

            /***********envoit du mail de confirmation àl'utilisateur**************/
            $messageEmail = (new \Swift_Message('Modification de votre compte'))
                ->setFrom('marlenebobat@gmail.com')
                ->setTo($utilisateur->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/modification.html.twig',
                        ['name'=> $utilisateur->getPseudo(),
                        'mail'=>$utilisateur->getEmail(),
                        'roles'=>$utilisateur->getRoles(),
                        'url'=> 'http://localhost:8000/accueil']),
                        'text/html'
                    );

            //envoi du mail
            $mailer->send($messageEmail);


            $message = $this->addFlash('modificationCompte','le compte utilisateur a bien été modifié');

            return $this->redirect('/accueil');

        }else{

            return $this->render('home/modificationCompte.html.twig',
                                    array('formulaire'=>$formulaireUser->createView(),
                                    ));

        }
    }

    /**
    *@Route("/monProfil/{id}", name="monProfil")
    **requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_USER')")
    **/
    public function espaceUser(Request $requete, $id, UserPasswordEncoderInterface $passwordEncoder,  \Swift_Mailer $mailer)
    {
        // accès au profil
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->find($id);

        //création du formulaire user

        $formulaireUser = $this->createForm(UserProfilType::class, $utilisateur);

        $formulaireUser->handleRequest($requete);

        if($formulaireUser->isSubmitted() && $formulaireUser->isValid()){

            // $commentaire->setArticle($article);

            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $formulaireUser->get('plainPassword')->getData()
                )
            );

            //récupération du controlleur
            $gestionnaire = $this->getDoctrine()->getManager();


            //enregistre l'ajout des données
            $gestionnaire->flush();

            /***********envoit du mail de confirmation àl'utilisateur**************/
            $messageEmail = (new \Swift_Message('Modification de votre compte'))
                ->setFrom('marlenebobat@gmail.com')
                ->setTo($utilisateur->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/modification.html.twig',
                        ['name'=> $utilisateur->getPseudo(),
                        'mail'=>$utilisateur->getEmail(),
                        'roles'=>$utilisateur->getRoles(),
                        'url'=> 'http://localhost:8000/accueil']),
                        'text/html'
                    );

            //envoi du mail
            $mailer->send($messageEmail);


            $message = $this->addFlash('modificationCompte','le compte utilisateur a bien été modifié');

            return $this->redirect('/accueil');

        }else{

            return $this->render('home/monProfil.html.twig',
                                    array('formulaire'=>$formulaireUser->createView(),
                                    ));



        }

    }

    /**
    **@Route("/delete/user/{id}", name="delete_user")
    *@Security("has_role('ROLE_USER')")
    **/

    public function deleteUser($id){
        // accès au profil
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->find($id);

        //récupération du controlleur
        $gestionnaire = $this->getDoctrine()->getManager();

        //enregistre l'ajout des données
        $gestionnaire->remove($utilisateur);

        //enregistre l'information de supresssion de l'objet
        $gestionnaire->flush();


        $message = $this->addFlash('suppressionUser','Utilisateur supprimé');


        return $this->redirect('/accueil');
    }


    /**
    **@Route("/demandeDelete/user/{id}", name="demande_delete")
    *@Security("has_role('ROLE_USER')")
    **/

    public function addDelete($id, \Swift_Mailer $mailer){
        // accès au profil
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->find($id);

        /***********envoit du mail de confirmation à l'utilisateur**************/
        $messageUser = (new \Swift_Message('Suppression de votre compte'))
            ->setFrom('marlenebobat@gmail.com')
            ->setTo($utilisateur->getEmail())
            ->setBody(
                $this->renderView(
                    'email/suppression.html.twig',
                    ['name'=> $utilisateur->getPseudo(),
                    'url'=> 'http://localhost:8000/accueil']),
                    'text/html'
                );

        /******envoit du mail à l'administrateur pour suppression du compte****/
        $messageAdmin = (new \Swift_Message('Suppression d\'un compte'))
            ->setFrom('marlenebobat@gmail.com')
            ->setTo('marlenebobat@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/suppressionMailAdmin.html.twig',
                    ['name'=> $utilisateur->getPseudo(),
                    'mail'=>$utilisateur->getEmail(),
                    'roles'=>$utilisateur->getRoles(),
                    'url'=> 'http://localhost:8000/parametre/modificationCompte/'.$utilisateur->getId().'']),
                    'text/html'
                );

        //envoi du mail
        $mailer->send($messageUser);
        $mailer->send($messageAdmin);

        $message = $this->addFlash('DemandeSuppressionUser','Votre demande de suppression vient d\'être transmise à l\'administrateur');


        return $this->redirect('/accueil');


    }



}
