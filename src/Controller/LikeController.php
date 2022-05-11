<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Event;
use App\Entity\User;
use App\Form\LikeType;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/like")
 */
class LikeController extends AbstractController
{
    /**
     * @Route("/", name="like_index", methods={"GET"})
     */
    public function index(LikeRepository $likeRepository): Response
    {
        return $this->render('like/index.html.twig', [
            'likes' => $likeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="like_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $like = new Like();
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($like);
            $entityManager->flush();

            return $this->redirectToRoute('like_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('like/new.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="like_show", methods={"GET"})
     */
    public function show(Like $like): Response
    {
        return $this->render('like/show.html.twig', [
            'like' => $like,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="like_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Like $like, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('like_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('like/edit.html.twig', [
            'like' => $like,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="like_delete", methods={"POST"})
     */
    public function delete(Request $request, Like $like, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$like->getId(), $request->request->get('_token'))) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        return $this->redirectToRoute('like_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/post/{id}/like", name="post_like")
     */
    public function like(Event $event, LikeRepository $likeRepo)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());

        if (!$user) {
            return $this->json(['code' => 403, 'error' => 'Vous devez être connecté !'], 403);
        }

        if ($event->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy(['event' => $event, 'user' => $user]);

            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json(['code' => 200, 'likes' => $likeRepo->getCountForEvents($event)], 200);
        }

        $like = new Like();
        $like->setEvent($event);
        $like->setUser($user);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['code' => 200, 'likes' => $likeRepo->getCountForEvents($event)], 200);
    }
}
