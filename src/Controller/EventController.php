<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/event')]
class EventController extends AbstractController
{

    #[Route('/', name: 'app_event')]
    public function listEvents(EventRepository $er): Response
    {
        $listEvents = $er->findAll();
        return $this->render('event/listEvents.html.twig',
            ['listE' => $listEvents]);
    }

    #[Route('/new', name: 'app_new')]
    public function new(Request $request, EntityManagerInterface $em)
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('app_event');
        }
        return $this->render('event/new.html.twig',
            ['formE' => $form->createView()]);
    }

    #[Route('/{id}', name: 'event_delete',requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $em, EventRepository $er, $id)
    {
        $event = $er->find($id);
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('app_event');


    }

    #[Route('/{id}/update', name: 'event_update')]
    public function update(Request $request, EntityManagerInterface $em, EventRepository $er, $id)
    {
        $event = $er->find($id);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/edit.html.twig', ['formE' => $form->createView()]);

    }
#[Route('/search', name: 'searchE')]
public function searchEvent(Request $r, EntityManagerInterface $em)
{
    $nom = $r->request->get('eventName');
    $q=$em->createQuery('select e from App\Entity\Event e where e.nom= :n');
    $q->setParameter('n',$nom);
    $events=$q->getResult();
    return $this->render('event/searchEvent.html.twig',["listE"=>$events]);
}


    #[Route('/search/{id}', name: 'searchE')]
    public function searchEventByIdClient(Request $r, EntityManagerInterface $em,$id)
    {
        $nom = $r->request->get('eventName');
        $q=$em->createQuery('select e from App\Entity\Event e where e.nom= :n');
        $q->setParameter('n',$nom);
        $events=$q->getResult();
        return $this->render('event/searchEvent.html.twig',["listE"=>$events]);
    }
    #[Route('/search/client/{id}', name: 'searchClientEvents')]
    public function searchClientEvents(int $id, EntityManagerInterface $em): Response
    {
        $query = $em->createQuery(
            'SELECT e 
         FROM App\Entity\Event e
         JOIN App\Entity\Inscription i WITH e.id = i.event
         WHERE i.client = :clientId'
        );
        $query->setParameter('clientId', $id);

        $events = $query->getResult();

        return $this->render('event/searchEventByClient.html.twig', [
            "listeE" => $events,
            "clientId" => $id
        ]);
    }
}
