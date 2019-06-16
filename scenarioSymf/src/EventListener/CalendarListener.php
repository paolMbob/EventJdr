<?php

namespace App\EventListener;

use App\Entity\Session;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use App\Repository\SessionRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarListener
{
    private $sessionRepo;
    private $router;

    public function __construct(SessionRepository $sessionRepository,UrlGeneratorInterface $router)
    {
        $this->sessionRepo = $sessionRepository;
        $this->router = $router;
    }

    public function load(CalendarEvent $calendar) : void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();


        // Ajout de la query du repository sessionRepository pour afficher les dates de fin et debut de session
        // Change booking.beginAt by your start date property
        $sessions = $this->sessionRepo
            ->createQueryBuilder('session')
            ->where('session.dateDebut BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($sessions as $session) {
            // création de l'évènement dans le calendrier avec la date de début et de fin de session
            $sessionEvent = new Event(
                $session->getId(),
                $session->getDateDebut(),
                $session->getDateFin(), // si la date de fin est null alors l'évènement sera créé sur la journée complète
                // $session->getLieu(),
                // $session->getMj()
                // $session->getPersonnageJoueur(),
                // $session->getScenario()->getNom()

            );

            //personalisation des évènements du calendrier
            $sessionEvent->setOptions([
                   'backgroundColor' => '#1A5276',
                   'borderColor' => '#1A5276',
               ]);

           $sessionEvent->addOption(
               'url',
               $this->router->generate('session_show', [
                   'id' => $session->getId(),
               ])
           );

           // finally, add the event to the CalendarEvent to fill the calendar
           $calendar->addEvent($sessionEvent);
       }
    }
}
