<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\EventType;
use App\Repository\EventRepository;
use App\Repository\EventTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{



    /**
     * @Route("/", name="app_event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findBy(['demande_status'=>'DemandeAccepted']),
        ]);
    }

    /**
     * @Route("/remove_event", name="remove_event", methods={"POST"})
     */
    public function remove_event(Request $request,EventRepository $eventRepository): Response
    {
        $fs = new Filesystem();
        $event=$eventRepository->find($request->get('dat'));
        if($event->getImageEvent()!="defaultimage.png")
            $fs->remove($this->getParameter('events_directory').'/'.$event->getImageEvent());
        try {
            $eventRepository->remove($event);
        } catch (OptimisticLockException | ORMException $e) {
        }
        return $this->redirectToRoute('index_calendar', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/update_event", name="update_event", methods={"POST"})
     */
    public function update_event(Request $request,EventRepository $eventRepository): Response
    {
        $event=$eventRepository->find($request->get('dat'));
        $event->setDateDebut(new \DateTimeImmutable($request->get('start')));
        $event->setDateFin(new \DateTimeImmutable($request->get('end')));
        try {
            $eventRepository->add($event);
        } catch (OptimisticLockException | ORMException $e) {
        }
        return $this->redirectToRoute('index_calendar', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/index_calendar", name="index_calendar", methods={"GET"})
     */
    public function index_calendar(EventRepository $eventRepository): Response
    {
        $clients= $this->getDoctrine()->getManager()->getRepository(User::class)->findBy(['role'=>'client']);
        $eventTypes=$this->getDoctrine()->getManager()->getRepository(EventType::class)->findAll();
        return $this->render('event/calendar.html.twig', [
            'events' => $eventRepository->findBy(['demande_status'=>'DemandeAccepted']),
            'clients'=>$clients,
            'et'=>$eventTypes,
        ]);
    }

    /**
     * @Route("/listedemande", name="app_event_listedemande", methods={"GET"})
     */
    public function listedemande(EventRepository $eventRepository): Response
    {
        return $this->render('event/listedemandeevent.html.twig', [
            'events' => $eventRepository->findBy(['demande_status'=>'DemandePending']),
        ]);
    }

    /**
     * @Route("/monreservation", name="app_event_monreservation", methods={"GET"})
     */
    public function monreservation(EventRepository $eventRepository,UserRepository $user): Response
    {
        return $this->render('event/getmyreservation.html.twig', [
            'events' => $eventRepository->findBy(['demande_status'=>'DemandePending','id_client'=>1]),
        ]);
    }

    /**
     * @Route("/listeevents", name="app_event_listeevents", methods={"GET"})
     */
    public function listeevents(EventRepository $eventRepository,UserRepository $user,Request $request, PaginatorInterface $paginator): Response
    {
        $donnees=$eventRepository->findBy(['demande_status'=>'DemandeAccepted','event_status'=>'publique']);
        $events = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        $session = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
        return $this->render('event/events.html.twig', [
            'events' => $events,
            'session' => $session,
        ]);
    }

    /**
     * @Route("/demandesearch", name="demandesearch", methods={"POST"})
     */
    public function demandesearch(Request $request)
    {
        $sea = $request->get('dat');
        $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->findBy(['demande_status'=>$sea]);
        return $this->render('event/filtrage.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * @Route("/tridatedebut", name="tridatedebut", methods={"POST"})
     */
    public function tridatedebut(Request $request)
    {
        if($request->get('date')=='ASC')
            $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->sortEventbyDateASCDQL();
        else
            $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->sortEventbyDateDESCDQL();
        return $this->render('event/trie_reserver.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * @Route("/searchevent", name="searchevent", methods={"POST"})
     */
    public function searchevent(Request $request)
    {
        $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->findEventByNameDQL($request->get('nom'));
        return $this->render('event/searcheventreserver.html.twig', array(
            'events' => $events,
        ));
    }


    /**
     * @Route("/searcheventresponsable", name="searcheventresponsable", methods={"POST"})
     */
    public function searcheventresponsable(Request $request)
    {
        $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->findEventByNameAcceptedDQL($request->get('nom'));
        return $this->render('event/searcheventresponsable.html.twig', array(
            'events' => $events,
        ));
    }



    /**
     * @Route("/new", name="app_event_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EventRepository $eventRepository,UserRepository $user): Response
    {
        $event = new Event();
        $form = $this->createForm(\App\Form\EventType::class, $event);
        $form->add('id_client',EntityType::class,
            [
                'class' => User::class,
                'query_builder' => function (UserRepository $user) {
                    return $user->createQueryBuilder('u')
                        ->orderBy('u.role','ASC')
                        ->andWhere('u.role = :role')
                        ->setParameter('role','client');
                },
                'choice_label' => function (User $user) {
                    return $user->getNom(). ' ' .$user->getPrenom();
                },
            ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image_event')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('events_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $event->setImageEvent($newFilename);
            }
            else {
                $event->setImageEvent('defaultimage.png');
            }
                $event->setIdResponsable($user->find(1));
                $event->setDemandeStatus("DemandeAccepted");
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
            }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/reserver", name="app_event_reserver", methods={"GET", "POST"})
     */
    public function reserver(Request $request, EventRepository $eventRepository,EventTypeRepository $etr,UserRepository $userR): Response
    {
        $event = new Event();
        $et = $etr->findAll();
        if ($request->isMethod('post')) {
            $brochureFile = $request->files->get('image_event');;
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('events_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $event->setImageEvent($newFilename);
            }
            else {
                $event->setImageEvent('defaultimage.png');
            }
                $event->setNomEvent($request->get('nom_event'));
                $event->setEventTheme($request->get('theme_event'));
                $event->setEventStatus($request->get('status'));
                $event->setDateDebut(new \DateTimeImmutable($request->get('date_debut')));
                $event->setDateFin(new \DateTimeImmutable($request->get('date_fin')));
                $event->setNbrParticipants($request->get('nbr_participants'));
                $event->setLieu($request->get('lieu'));
                $event->setEventDescription($request->get('description'));
                $event->setIdType($etr->find($request->get('event_type')));
                $event->setIdClient($userR->find(1));

                $event->setDemandeStatus("DemandePending");
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_monreservation', [], Response::HTTP_SEE_OTHER);
            }


        return $this->render('event/newreserver.html.twig', [
            'event' => $event,
            'types' => $et
        ]);
    }

    /**
     * @Route("/newcalendar", name="newcalendar", methods={"GET", "POST"})
     */
    public function newcalendar(Request $request, EventRepository $eventRepository,EventTypeRepository $etr,UserRepository $userR): Response
    {
        $event = new Event();

        if ($request->isMethod('POST')) {
            $event->setImageEvent('defaultimage.png');
            $event->setNomEvent($request->get('nom_event'));
            $event->setEventTheme('');
            $event->setEventStatus('publique');
            $event->setDateDebut(new \DateTimeImmutable($request->get('start')));
            $event->setDateFin(new \DateTimeImmutable($request->get('end')));
            $event->setNbrParticipants($request->get('nbr'));
            $event->setLieu($request->get('lieu'));
            $event->setEventDescription('');
            $event->setIdType($etr->find($request->get('event_type')));
            $event->setIdClient($userR->find($request->get('client')));
            $event->setIdResponsable($userR->find($this->getUser()->getId()));
            $event->setDemandeStatus("DemandeAccepted");
            $eventRepository->add($event);
        }
        return $this->redirectToRoute('index_calendar', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/editreserver/{id}", name="app_event_editreserver", methods={"GET", "POST"})
     */
    public function editreserver(Request $request, EventRepository $eventRepository,EventTypeRepository $etr,UserRepository $userR): Response
    {

        $event = $eventRepository->find($request->get('id'));
        $et = $etr->findAll();
        if ($request->isMethod('post')) {

            $brochureFile = $request->files->get('image_event');;
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('events_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $fs = new Filesystem();
                $fs->remove($this->getParameter('events_directory').'/'.$event->getImageEvent());
                $event->setImageEvent($newFilename);
            }

                $event->setNomEvent($request->get('nom_event'));
                $event->setEventTheme($request->get('theme_event'));
                $event->setEventStatus($request->get('status'));
                $event->setDateDebut(new \DateTimeImmutable($request->get('date_debut')));
                $event->setDateFin(new \DateTimeImmutable($request->get('date_fin')));
                $event->setNbrParticipants($request->get('nbr_participants'));
                $event->setLieu($request->get('lieu'));
                $event->setEventDescription($request->get('description'));
                $event->setIdType($etr->find($request->get('event_type')));
                $event->setIdClient($userR->find($this->getUser()->getId()));

                $event->setDemandeStatus("DemandePending");
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_monreservation', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('event/editreserver.html.twig', [
            'event' => $event,
            'types' => $et
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_event_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(\App\Form\EventType::class, $event);
        $form->add('id_client',EntityType::class,
            [
                'class' => User::class,
                'query_builder' => function (UserRepository $user) {
                    return $user->createQueryBuilder('u')
                        ->orderBy('u.role','ASC')
                        ->andWhere('u.role = :role')
                        ->setParameter('role','client');
                },
                'choice_label' => function (User $user) {
                    return $user->getNom(). ' ' .$user->getPrenom();
                },
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('image_event')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('events_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $event->setImageEvent($newFilename);
            }
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
            }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_event_delete", methods={"POST","GET"})
     */
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $fs = new Filesystem();
        $event=$eventRepository->find($request->get('id'));
        if($event->getImageEvent()!="defaultimage.png")
        $fs->remove($this->getParameter('events_directory').'/'.$event->getImageEvent());
        $eventRepository->remove($event);
        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }



    /**
     * @Route("/deletereser/{id}", name="app_event_deletereser", methods={"POST","GET"})
     */
    public function deletereser(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $fs = new Filesystem();
        $event=$eventRepository->find($request->get('id'));
        $fs->remove($this->getParameter('events_directory').'/'.$event->getImageEvent());
        $eventRepository->remove($event);
        return $this->redirectToRoute('app_event_monreservation', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/updateaccepterrefuser/{id}/{action}", name="app_event_acceptrefuser", methods={"POST","GET"})
     */
    public function updateaccepterrefuser(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $event=$eventRepository->find($request->get('id'));
        if($request->get('action')=='accept') {
            $event->setDemandeStatus('DemandeAccepted');
        } else {
            $event->setDemandeStatus('DemandeRefused');
        }
        $eventRepository->add($event);
        return $this->redirectToRoute('app_event_listedemande', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/participate/{id}", name="event_participate", methods={"GET", "POST"})
     */
    public function participate(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($request->get('id') + 0);
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
        $account_sid = 'ACb3d0e0eced06ffd7beec5ff336b6b389';
        $auth_token = '86ad8d8afc32147f5f840acebf0337ef';
        $twilio_number = "+15038324523";
        $client = new Client($account_sid, $auth_token);
        $curlOptions = [ CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
        $client->setHttpClient(new CurlClient($curlOptions));
        $client->messages->create(
            '+21653328112',
            array(
                'from' => $twilio_number,
                'body' => 'vous avez participé a l evenement ' . $event->getNomEvent() .' De   '.$event->getDateDebut()->format('d/m/Y').'   Jusqu à   '.$event->getDateFin()->format('d/m/Y')
            ));
        $event->addUser($user);
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_event_listeevents');
    }

    /**
     * @Route("/annulation_participate/{id}", name="annulation_participate", methods={"GET", "POST"})
     */
    public function annulation_participate(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($request->get('id') + 0);
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
        $event->removeUser($user);
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_event_listeevents');
    }




}
