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




class AccueilController extends AbstractController
{
    /**
    *@Route("/accueil", name="accueil")
    **/
    public function accueil()
    {
        /**********AFFICHE LES 3 DERNIERS SCENARIOS*************/

        //recuperation du depot Scenarios (la collection de la classe)
        $depot = $this->getDoctrine()->getRepository(Scenario::class);

        //accès au tableau des 3 derniers scénarios enregistrer
        $listeScenarios = $depot->findDernierScenario();

        return $this->render('home/accueil.html.twig',
                            array('scenarios'=>$listeScenarios,
                            ));

    }
    ;

}
