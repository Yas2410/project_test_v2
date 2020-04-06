<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventController extends AbstractController
{
    /**
     * //* @Route("/admin/events", name="admin_events_list")
     * @param EventRepository $eventRepository
     * @return Response
     */

    public function events(EventRepository $eventRepository)
    {
        $events = $eventRepository->findAll();
        return $this->render('admin/events/events.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @route("admin/event/show/{id}", name="admin_event_show")
     * @param EventRepository $eventRepository
     * @param $id
     * @return Response
     */
    public function event(EventRepository $eventRepository, $id)
    {
        $event = $eventRepository->find($id);

        return $this->render('admin/events/event.html.twig', [
            'events' => $event
        ]);
    }

    /**
     * @route("admin/event/insert", name="admin_insert_event")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $slugger
     * @return Response
     */

    public function insertEvent(Request $request,
                                  EntityManagerInterface $entityManager,
                                  SluggerInterface $slugger
    )
    {
        $event = new Event();
        $formEvent = $this->createForm(EventType::class, $event);
        $formEvent->handleRequest($request);

        if ($formEvent->isSubmitted() && $formEvent->isValid()) {

            $eventFile = $formEvent->get('eventfile')->getData();

            if ($eventFile) {

                $originalFilename = pathinfo($eventFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $eventFile->guessExtension();

                $eventFile->move(
                    $this->getParameter('eventFile_directory'),
                    $newFilename);

                $event->setEventFile($newFilename);
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', "L'évènement a bien été créé !");

        }
        return $this->render('admin/events/insert_event.html.twig', [
            'formEvent' => $formEvent->createView()
        ]);

    }

    /**
     * @route("admin/event/search", name="admin_search_event")
     * @param EventRepository $eventRepository
     * @param Request $request
     * @return Response
     */
    public function searchByEvent(EventRepository $eventRepository, Request $request)
    {
        $search = $request->query->get('search');
        $events = $eventRepository->getByWordInEvent($search);

        return $this->render('admin/events/search_event.html.twig', [
            'search' => $search, 'events' => $events
        ]);
    }

    /**
     * @route("admin/event/update/{id}", name="admin_update_event")
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function updateEvent(
        Request $request,
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $event = $eventRepository->find($id);
        $formEvent = $this->createForm(EventType::class, $event);
        $formEvent->handleRequest($request);
        if ($formEvent->isSubmitted() && $formEvent->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('sucess', "L'évènement a bien été modifié !");
        }

        return $this->render('admin/events/update_event.html.twig', [
            'formEvent'=>$formEvent->createView()
        ]);
    }

    /**
     * @route("admin/event/delete/{id}", name="admin_delete_event")
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteEvent(
        Request $request,
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $event = $eventRepository->find($id);
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('sucess', "L'évènement a bien été supprimé !");

        return $this->redirectToRoute('admin_events_list');
    }

}