<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
     * @Route("/listedemande", name="app_event_listedemande", methods={"GET"})
     */
    public function listedemande(EventRepository $eventRepository): Response
    {
        return $this->render('event/listedemandeevent.html.twig', [
            'events' => $eventRepository->findBy(['demande_status'=>'DemandePending']),
        ]);
    }



    /**
     * @Route("/new", name="app_event_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
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
                $event->setDemandeStatus("DemandeAccepted");
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reserver", name="app_event_reserver", methods={"GET", "POST"})
     */
    public function reserver(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

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
                $event->setDemandeStatus("DemandeAccepted");
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('event/newreserver.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}/edit", name="app_event_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
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
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
            }
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
        $fs->remove($this->getParameter('events_directory').'/'.$event->getImageEvent());
        $eventRepository->remove($event);
        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
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


}
