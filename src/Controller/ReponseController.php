<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reponse")
 */

class ReponseController extends AbstractController
{

    /**
     * @Route("/{id}", name="app_reponse_index")
     */
    public function index($id): Response
    {
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        $reponses=$this->getDoctrine()->getRepository(Reponse::class)->findBy([
            'reclamations' => $id
        ]);
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
            'reclamations' => $reclamations,
        ]);
    }

    /**
     * @Route("/Admin/{id}", name="app_reponse_index_Admin")
     */
    public function indexAdmin($id): Response
    {
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        $reponses=$this->getDoctrine()->getRepository(Reponse::class)->findBy([
            'reclamations' => $id
        ]);
        return $this->render('reponse/indexAdmin.html.twig', [
            'reponses' => $reponses,
            'reclamations' => $reclamations,

        ]);
    }


    /**
     * @Route("/new/{id}", name="app_reponse_new", methods={"GET", "POST"})
     */
    public function new(Request $request,$id, ReponseRepository $reponseRepository): Response
    {
        $reponse = new Reponse();
        $reclamation=$this->getDoctrine()->getRepository(Reclamation::class)->find($id);

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reponse->setReclamations($reclamation);

            $reponseRepository->add($reponse);
            return $this->redirectToRoute('app_reponse_index_Admin', [
                'id' =>  $id,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/add.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_reponse_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reponse $reponse, ReponseRepository $reponseRepository): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $reponseRepository->add($reponse);
            return $this->redirectToRoute('app_reponse_index_Admin', [
                'id' =>  $reponse->getReclamations()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_reponse_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->find($id);

        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($reponse);
        $entityyManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('app_reponse_index_Admin', [
            'id' =>  $reponse->getReclamations()->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

}
