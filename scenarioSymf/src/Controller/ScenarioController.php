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
use App\Form\FormCommentaireType;
use App\Form\ScenarioChangeType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class ScenarioController extends AbstractController
{
    /**
    *@Route("/scenarios", name="scenarios")
    **/
    public function listeScenarios(Request $requetes)
    {


    /************************Univers***************************/
        //recuperation du depot Univers (la collection de la classe)
        $depotUnivers = $this->getDoctrine()->getRepository(Univers::class);

        //accès au tableau de l'univers
        $listeUnivers = $depotUnivers->findAll();

        // objet choix de l'univers à passer en parametre de createForm afin de faire un setUnivers
        $univers = new UniversOption();


        //accès au formulaire
        $formulaire = $this->createForm(UniversOptionType::class, $univers,array('liste'=> $listeUnivers));

        $formulaire->handleRequest($requetes);

        /************************Scénarios*****************************/
        if($formulaire->isSubmitted() && $formulaire->isValid()){

            // récupération de l'objet univers grâce à l'id univer passé dans le formulaire
            $universScenarios = $depotUnivers->find($univers->getUnivers());

            //on récupère la liste des scénarios liées à cet univers
            $listeScenarios = $universScenarios->getScenarios();


            // dd($listeScenarios);


            return $this->render('scenario/scenarioUnivers.html.twig',
                                    array("univers"=>$listeUnivers,
                                            "scenarios"=>$listeScenarios,
                                            "formulaire"=>$formulaire->createView()
                                          ));
          }else{
              return $this->render('scenario/scenarioUnivers.html.twig',
                                      array("univers"=>$listeUnivers,
                                      "formulaire"=>$formulaire->createView()
                                            ));
          }
    }

    /**
    *@Route("/scenario/{id}", name="scenario",
    *requirements = {"id":"\d+"})
    **/
    public function scenario($id){

        //récuperation du depot Article (la collection de la classe)
        $depotScenario = $this->getDoctrine()->getRepository(Scenario::class);

        //accès au tableau de Article
        $scenario = $depotScenario->find($id);

        //recuperation de la liste des utilisateurs pour affichage du pseudo de l'utilisateurs
        $depotUser = $this->getDoctrine()->getRepository(User::class);

        //accès au tableau des User
        $listeUser = $depotUser->findAll();


            return $this->render('scenario/scenario.html.twig',
                                    array("scenario"=>$scenario,
                                            "listeUser"=>$listeUser));

    }

    /**
    *@Route("/ajout", name="ajout")
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function addScenario(Request $requete)
    {

        //creation de l'objet article
        $scenario = new Scenario();

        $formulaire = $this->createForm(FormScenarioType::class, $scenario);


        /************enregistrement des valeurs****************/

        $formulaire->handleRequest($requete);

        if($formulaire->isSubmitted() && $formulaire->isValid()){

            //accès à l'image pour création
            $file = $formulaire->get('image')->getData();

            $fileName= $scenario->getNom().md5(uniqid()).".".$file->guessExtension();

            //deplacer l'image dans le dossier des images
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            }catch (FileException $e){
                throw new NotFoundHttpException('erreur dans le tranfert de l\'image');
            }

            //set image dans la classe scénario
            $scenario->setImage($fileName);
            //enregistre dans la base de données

                //récupération du controlleur
            $gestionnaire = $this->getDoctrine()->getManager();

                //accès au service Doctrine et confier à gestionnaire
            $gestionnaire->persist($scenario);

                //enregistre l'ajout des données
            $gestionnaire->flush();

            $message = $this->addFlash('scenario','Scénario créé');

            //redirection vers l'accueil
            return $this->redirect('accueil');

        }else{
            return $this->render('scenario/ajout.html.twig',
                                    array('formulaire'=>$formulaire->createView()
                                ));
        }

    }


    /**
    *@Route("/ajoutUnivers", name="ajoutUnivers")
    *@Security("has_role('ROLE_ADMIN')")
    **/

    public function addUnivers(Request $requete)
    {

        //création de l'objet univers
        $univers = new Univers();

        $formulaire = $this->createForm(FormUniversType::class, $univers);

        //recuperation du depot univers (la collection de la classe)
        $depot = $this->getDoctrine()->getRepository(Univers::class);


        //accès au tableau univers
        $univers = $depot->findAll();

        /**********enregistrement des valeurs*************/
        $formulaire->handleRequest($requete);

        if($formulaire->isSubmitted() && $formulaire->isValid()){

            //enregistre dans la base de données

                //récupération du controlleur
            $gestionnaire = $this->getDoctrine()->getManager();

                //accès au service Doctrine et confier à gestionnaire
            $gestionnaire->persist($univers);

                //enregistre l'ajout des données
            $gestionnaire->flush();

            $message = $this->addFlash('univers','Univers créé');

            //redirection vers l'accueil
            return $this->redirect('accueil');

        }else{
            return $this->render('scenario/ajoutUnivers.html.twig',
                                    array('formulaire'=>$formulaire->createView(),
                                    'univers'=>$univers
                                ));
        }
    }

    /**
    *@Route("/commentaire/{id}", name="commentaire",
    *requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_USER')")
    **/
    public function creationCommentaire(Request $requete, $id){

        /***************gestion d'erreur scénario non trouvé******************/

        //récupération de l'objet article concerné par l'ajout du Commentaire
        $depot = $this->getDoctrine()->getRepository(Scenario::class);

        $scenario = $depot->find($id);

        if($scenario == null){
            throw new \Exception('ce scénario n\'existe pas');
        }

        //création de l'objet commentaire
        $commentaire = new Commentaire();

        //création du formulaire commentaire
        $formCommentaire= $this->createForm(FormCommentaireType::class,$commentaire);

        // enregistrement des valeurs
        $formCommentaire->handleRequest($requete);

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){

            //on ajoute l'id du scenario correspondant au commentaire saisi
            $commentaire->setScenario($scenario);
            $commentaire->setAuteur($this->getUser());
            //récupération du controlleur
            $gestionnaire = $this->getDoctrine()->getManager();
            //accès au service Doctrine et confier à gestionnaire
            $gestionnaire->persist($commentaire);

            //enregistre l'ajout des données
            $gestionnaire->flush();
            $message = $this->addFlash('commentaire','le comentaire vient d\'être ajouté au scénario');

            return $this->redirect('/accueil');

        }else{

            //rendre le formulaire d'ajout du commentaire
            return $this->render('scenario/commentaire.html.twig',
                                    array('formulaire'=>$formCommentaire->createView(),
                                            "titreScenario"=>$scenario));
        }


    }

    /**
    *@Route("/supprimerScenario/{id}", name="supprimerScenario",
    *requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function supprimerScenario($id){

        //recuperation du depot scenario(la collection de la classe)
        $depot = $this->getDoctrine()->getRepository(Scenario::class);

        //accès au scenario
        $scenario = $depot->find($id);


        //enregistre dans la base de données
            //récupération du controlleur
        $gestionnaire = $this->getDoctrine()->getManager();

        //accès au service Doctrine et confier à gestionnaire
        $gestionnaire->remove($scenario);

        //enregistre l'information de supresssion de l'objet
        $gestionnaire->flush();

        $message = $this->addFlash('suppression','scénario supprimé');
        //affichage des articles
        return $this->redirect('/accueil');


    }

    /**
    *@Route("supprimerCommentaire/{id}", name="supprimerCommentaire",
    *requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_USER')")
    **/

    public function supprimerCommentaire($id){

        //recuperation du depot commentaire(la collection de la classe)
        $depot = $this->getDoctrine()->getRepository(Commentaire::class);

        //accès au commentaire
        $commentaire = $depot->find($id);


        //enregistre dans la base de données
            //récupération du controlleur
        $gestionnaire = $this->getDoctrine()->getManager();

        //accès au service Doctrine et confier à gestionnaire
        $gestionnaire->remove($commentaire);

        //enregistre l'information de supresssion de l'objet
        $gestionnaire->flush();

        $message = $this->addFlash('suppressionCommentaire','commentaire supprimé');
        //affichage des articles
        return $this->redirect('/accueil');

    }


    /**
    *@Route("modificationScenario/{id}", name="modificationScenario",
    *requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_ADMIN')")
    **/

    public function changeScenario($id, Request $requete){

        //recuperation du depot Scenario (la collection de la classe)
            $depot = $this->getDoctrine()->getRepository(Scenario::class);


            //accès au tableau du scénario
            $scenario = $depot->find($id);


            // ajouter le form de l'article associé
            $formulaire = $this->createForm(ScenarioChangeType::class, $scenario);


            /************enregistrement des valeurs****************/
            $formulaire->handleRequest($requete);

            if($formulaire->isSubmitted() && $formulaire->isValid()){

                //récupération du controlleur
                $gestionnaire = $this->getDoctrine()->getManager();


                //enregistre les cgmt
                $gestionnaire->flush();

                $message = $this->addFlash('modification','Scénario modifié');

                return $this->redirect('/accueil');


            }else{
                //affichage des articles
                return $this->render('scenario/modificationScenario.html.twig',
                                        array('formulaire'=>$formulaire->createView()
                                    ));

            }
    }

    /**
    *@Route("modificationUnivers/{id}", name="modificationUnivers",
    *requirements = {"id":"\d+"})
    *@Security("has_role('ROLE_ADMIN')")
    **/
    public function changeUnivers($id, Request $requete){

        //recuperation du depot univers (la collection de la classe)
            $depot = $this->getDoctrine()->getRepository(Univers::class);


            //accès au tableau univers
            $univers = $depot->find($id);


            // ajouter le form de l'univers associé
            $formulaire = $this->createForm(FormUniversType::class, $univers);


            /************enregistrement des valeurs****************/
            $formulaire->handleRequest($requete);

            if($formulaire->isSubmitted() && $formulaire->isValid()){

                //récupération du controlleur
                $gestionnaire = $this->getDoctrine()->getManager();


                //enregistre les cgmt
                $gestionnaire->flush();

                $message = $this->addFlash('modificationUnivers','Univers modifié');

                return $this->redirect('/accueil');


            }else{
                //affichage des univers
                return $this->render('scenario/modificationUnivers.html.twig',
                                        array('formulaire'=>$formulaire->createView()
                                    ));

            }
        }

        /**
        *@Route("modificationCommentaire/{id}", name="modificationCommentaire",
        *requirements = {"id":"\d+"})
        *@Security("has_role('ROLE_USER')")
        **/

        public function changeCommentaire($id, Request $requete){

                //recuperation du depot Commentaire (la collection de la classe)
                $depot = $this->getDoctrine()->getRepository(Commentaire::class);


                //accès au commentaire
                $commentaire= $depot->find($id);

                $scenarioId= $commentaire->getScenario()->getId();


                // ajouter le form de l'article associé
                $formulaire = $this->createForm(FormCommentaireType::class, $commentaire);


                /************enregistrement des valeurs****************/
                $formulaire->handleRequest($requete);

                if($formulaire->isSubmitted() && $formulaire->isValid()){

                    //récupération du controlleur
                    $gestionnaire = $this->getDoctrine()->getManager();


                    //enregistre les cgmt
                    $gestionnaire->flush();

                    $message = $this->addFlash('modificationCommentaire','Commentaire modifié');

                    return $this->redirect('/scenario/'. $scenarioId. '');


                }else{
                    //affichage des articles
                    return $this->render('scenario/modificationCommentaire.html.twig',
                                            array('formulaire'=>$formulaire->createView()
                                        ));

                }
        }

}
