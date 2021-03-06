<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Entity\User;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Scenario;
use App\Entity\MaitreDuJeu;
use App\Entity\PersonnageJoueur;
use App\Form\PersonnageJoueurType;


class SessionController extends AbstractController
{

    //permet d'accéder à la session en fonction de l'id scénario depuis un scénario
    /**
   *@Route("/session/{id}", name="session")
   *requirements = {"id":"\d+"})
   **/
   public function dateSession($id){

       //recupération du du dépôt Scenario
       $depotSession = $this->getDoctrine()->getRepository(Scenario::class);

       //on recherche les scenario concerné
       $scenario = $depotSession->find($id);

       //on recherche les sessions associées à l'id du scenario
       $sessions = $scenario->getSessions();


       return $this->render('scenario/session.html.twig',
                       array('sessions'=>$sessions,
                            'scenario'=>$scenario));

   }

   //permet d'accéder aux sessions du calendrier
   /**
   *@Route("/calendar", name="session_calendar", methods={"GET"})
   **/
   public function calendar(): Response
   {

       return $this->render('session/calendrier.html.twig');
   }

    //permet d'accéder à la liste de toutes les sessions
    /**
     * @Route("calendrier/index", name="session_index", methods={"GET"})
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        //on recherche les sessions associées à l'id du scenario
        $sessions = $sessionRepository->findAll();

        return $this->render('session/index.html.twig', [
            'sessions'=>$sessions,
        ]);
    }

    //permet d'ajouter une autre session
    /**
     * @Route("calendrier/new/{id}", name="session_new", methods={"GET","POST"})
     ** requirements = {"id":"\d+"})
     ** @Security("has_role('ROLE_ADMIN')")
     */
    public function new(Request $request, $id): Response
    {

        //recuperation du user
        $utilisateur = $this->getDoctrine()->getRepository(User::class)->find($id);

        //recuperation du maitre du jeu
        $mj= $this->getDoctrine()->getRepository(MaitreDuJeu::class)->findOneBy(['user'=>$utilisateur]);


        $session = new Session();

        $form = $this->createForm(SessionType::class, $session);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //on ajoute le profil mj de l'utilisateur actuelle
            $session->setMj($mj);
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    //permet de voir le détail de l'id session
    /**
     * @Route("calendrier/event/{id}", name="session_show", methods={"GET"})
     **/
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session
        ]);
    }

    /**permet la modification d'une session**/
    /**
     * @Route("calendrier/event/{id}/edit", name="session_edit", methods={"GET","POST"})
     **/
    public function edit(Request $request, Session $session): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_index', [
                'id' => $session->getId(),
            ]);
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**permet la suppression d'un personnagejoueur d'une session lors de l'edition de la session**/
    /**
    * @Route("personnagejoueur/delete/{id}/{session}", name="pj_session_delete", methods={"GET","POST"})
    */
    public function deletePjSession($id, Session $session, \Swift_Mailer $mailer)
    {
        //recuperation du depot personnage joueur
        $pj = $this->getDoctrine()->getRepository(PersonnageJoueur::class)->find($id);

        //récupération du mail de l'utilisateur personnage joueur
        $user = $pj->getUser()->getEmail();

        /***********envoit du mail de confirmation à l'utilisateur**************/
        $messageUser = (new \Swift_Message('personnage supprimé'))
            ->setFrom('marlenebobat@gmail.com')
            ->setTo($user)
            ->setBody(
                $this->renderView(
                    'email/deletePersonnage.html.twig',
                    ['session'=> $session,
                    'personnage'=> $pj->getRace()->getRace(),
                    'url'=> 'http://localhost:8000/accueil']),
                    'text/html'
                );

        //envoi du mail
        $mailer->send($messageUser);

        //accès au service Doctrine et confier à gestionnaire
        $entityManager = $this->getDoctrine()->getManager();

        //suppression du pj de l'entity PersonnageJoueur et de l'entity Session
        $entityManager->remove($pj);

        // //enregistre l'information de supresssion de l'objet
        $entityManager->flush();

        $message = $this->addFlash('suppressionPj','personnage joueur supprimé de la session');
        //affichage des articles
        return $this->redirectToRoute('session_show', ['id'=>$session->getId()]);
    }

    /**
     * @Route("calendrier/event/{id}", name="session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }

    /** Permet l'inscription des joueurs à une session **/
    /**
     * @Route("session/inscription/{id}/{user}", name="session_inscription", methods={"GET","POST"})
     */
     public function inscription(Request $requete, Session $session, $id,$user, \Swift_Mailer $mailer): Response
     {
         /**************************création du personnage joueur**********************************/


         //creation de l'objet PersonnageJoueur
         $pj = new PersonnageJoueur();

         $formulaire = $this->createForm(PersonnageJoueurType::class, $pj);

         //recuperation du mail du pj pour l'envoit
         $userMail= $this->getDoctrine()->getRepository(User::class)->find($user)->getEmail();

         // $personnageJoueur= $this->getDoctrine()->getRepository(MaitreDuJeu::class)->findOneBy(['user'=>$utilisateur]);

         /************enregistrement des valeurs du pj****************/

         $formulaire->handleRequest($requete);

         if($formulaire->isSubmitted() && $formulaire->isValid())
         {
             // insertion de l'utilisateur courant
             $pj->setUser($this->getUser());
             //insertion de la session en cours
             $pj->addSession($session);

                 //récupération du controlleur
             $gestionnaire = $this->getDoctrine()->getManager();

             //accès au service Doctrine et confier à gestionnaire
             $gestionnaire->persist($pj);

             //enregistre l'ajout des données
             $gestionnaire->flush();


             /***********envoit du mail de confirmation à l'utilisateur**************/
             $messageUser = (new \Swift_Message('inscription à une session'))
                 ->setFrom('marlenebobat@gmail.com')
                 ->setTo($userMail)
                 ->setBody(
                     $this->renderView(
                         'email/inscriptionPersonnage.html.twig',
                         ['session'=> $session,
                         'personnage'=> $pj->getRace()->getRace(),
                         'url'=> 'http://localhost:8000/accueil']),
                         'text/html'
                     );

             //envoi du mail
             $mailer->send($messageUser);

             $message = $this->addFlash('inscription','votre participation à été ajouté avec succès');

             //on récupère la durée à afficher dans le récap d'une session
             $duree=  $session->getDateFin() != NULL ? date_diff($session->getDateFin(), $session->getDateDebut())->format('%h heures') : "non connue";

             // redirection vers la session
             return $this->redirectToRoute('session_show', ['id'=>$session->getId()]);

         }else{
             return $this->render('session/session_inscription.html.twig',
                                     array('formulaire'=>$formulaire->createView()
                                 ));
         }
    }

}
