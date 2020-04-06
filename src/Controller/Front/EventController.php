<?php

namespace App\Controller\Front;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("front/events", name="front_events")
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function events(EventRepository $eventRepository)
    {
        $events = $eventRepository->findAll();
        return $this->render('front/event/events.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @route("front/event/show/{id}", name="front_event")
     * @param EventRepository $eventRepository
     * @param $id
     * @return Response
     */
    public function event(EventRepository $eventRepository, $id)
    {
        $event = $eventRepository->find($id);

        return $this->render('front/event/event.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @route("front/event/search", name="front_event_search")
     * @param EventRepository $eventRepository
     * @param Request $request
     * @return Response
     */
    public function searchByEvent(EventRepository $eventRepository, Request $request)
    {
        $search = $request->query->get('search');
        $events = $eventRepository->getByWordInEvent($search);

        return $this->render('front/event/search.html.twig', [
            'search' => $search, 'events' => $events
        ]);
    }

}