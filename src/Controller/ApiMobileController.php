<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventType;
use App\Repository\EventRepository;

use App\Repository\EventTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api")
 */
class ApiMobileController extends AbstractController
{
    /**
     * @Route("/getalleventtypess", name="get_all_eventtypess" , methods={"GET"})
     */
    public function getalleventtypess(EventTypeRepository $eventtyperepo): Response
    {
        $eventtypes=$eventtyperepo->findAll();
        $ser= new Serializer([new ObjectNormalizer()]);
        $e=$ser->normalize($eventtypes  ,JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['events']]);
        return new JsonResponse($e);
    }

    /**
     * @Route("/getallclients", name="get_all_getallclients" , methods={"GET"})
     */
    public function getallclients(UserRepository $user): Response
    {
        $users=$user->findBy(["role"=>'client']);
        $ser= new Serializer([new ObjectNormalizer()]);
        $e=$ser->normalize($users);
        return new JsonResponse($e);
    }

    /**
     * @Route("/getallevents", name="get_all_getallevents" , methods={"GET"})
     */
    public function getallevents(EventRepository $eventrepo): Response
    {
        $events=$eventrepo->findAll();
        $ser= new Serializer([new DateTimeNormalizer() ,new ObjectNormalizer()]);
        $e=$ser->normalize($events,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['likes']]);
        return new JsonResponse($e);
    }

    /**
     * @Route("/ajoutereventtypejson/{libelle}", name="ajoutereventtypejson" , methods={"GET","POST"})
     */
    public function ajoutereventtypejson(Request $request, EntityManagerInterface $entityManager) : Response {
        $eventtype= new EventType();
        $eventtype->setLibelle($request->get('libelle'));
        $entityManager->persist($eventtype);
        $entityManager->flush();
        return new JsonResponse("successfully created");
    }

    /**
     * @Route("/modifiereventtypejson/{id}/{libelle}", name="modifiereventtypejson" , methods={"GET","POST"})
     */
    public function modifiereventtypejson(Request $request,EventTypeRepository $e,EntityManagerInterface $entityManager) : Response {
        $eventtype=$e->find($request->get('id'));
        $eventtype->setLibelle($request->get('libelle'));
        $entityManager->persist($eventtype);
        $entityManager->flush();
        return new JsonResponse("successfully updated");
    }

    /**
     * @Route("/supprimereventtypejson/{id}", name="supprimereventtypejson" , methods={"GET","POST"})
     */
    public function supprimereventtypejson(Request $request,EventTypeRepository $e,EntityManagerInterface $entityManager) : Response {
        $eventtype=$e->find($request->get('id'));
        $entityManager->remove($eventtype);
        $entityManager->flush();
        return new JsonResponse("successfully removed");
    }


    /**
     * @Route("/ajoutereventjson/{nom_event}/{event_description}/{event_theme}/{date_debut}/{date_fin}/{event_status}/{id_client}/{id_type}/{nbr_participants}/{lieu}/{image_event}", name="ajoutereventjson" , methods={"GET","POST"})
     */
    public function ajoutereventjson(Request $request,EventTypeRepository $etr,UserRepository $userR,EntityManagerInterface $entityManager) : Response {
        $event= new Event();
        $event->setImageEvent($request->get('image_event'));
        $event->setNomEvent($request->get('nom_event'));
        $event->setEventTheme($request->get('event_theme'));
        $event->setEventStatus($request->get('event_status'));
        $event->setDateDebut(new \DateTimeImmutable($request->get('date_debut')));
        $event->setDateFin(new \DateTimeImmutable($request->get('date_fin')));
        $event->setNbrParticipants($request->get('nbr_participants'));
        $event->setLieu($request->get('lieu'));
        $event->setEventDescription($request->get('event_description'));
        $event->setIdType($etr->find($request->get('id_type')));
        $event->setIdClient($userR->find($request->get('id_client')));
        $event->setDemandeStatus("DemandeAccepted");
        $entityManager->persist($event);
        $entityManager->flush();
        return new JsonResponse("event successfully created");
    }

    /**
     * @Route("/modifiereventjson/{id}/{nom_event}/{event_description}/{event_theme}/{date_debut}/{date_fin}/{event_status}/{id_client}/{id_type}/{nbr_participants}/{lieu}/{image_event}", name="modifiereventjson" , methods={"GET","POST"})
     */
    public function modifiereventjson(Request $request,EventTypeRepository $etr,UserRepository $userR,EventRepository $e,EntityManagerInterface $entityManager) : Response {
        $event=$e->find($request->get('id'));
        $event->setImageEvent($request->get('image_event'));
        $event->setNomEvent($request->get('nom_event'));
        $event->setEventTheme($request->get('event_theme'));
        $event->setEventStatus($request->get('event_status'));
        $event->setDateDebut(new \DateTimeImmutable($request->get('date_debut')));
        $event->setDateFin(new \DateTimeImmutable($request->get('date_fin')));
        $event->setNbrParticipants($request->get('nbr_participants'));
        $event->setLieu($request->get('lieu'));
        $event->setEventDescription($request->get('event_description'));
        $event->setIdType($etr->find($request->get('id_type')));
        $event->setIdClient($userR->find($request->get('id_client')));
        $event->setDemandeStatus("DemandeAccepted");
        $entityManager->persist($event);
        $entityManager->flush();
        return new JsonResponse("event successfully updated");
    }

    /**
     * @Route("/supprimereventjson/{id}", name="supprimereventjson" , methods={"GET","POST"})
     */
    public function supprimereventjson(Request $request,EventRepository $e,EntityManagerInterface $entityManager) : Response {
        $event=$e->find($request->get('id'));
        $entityManager->remove($event);
        $entityManager->flush();
        return new JsonResponse("event successfully removed");
    }


}
