<?php

namespace App\Controller;

use App\Entity\Comptabilite;
use App\Form\ComptabiliteType;
use App\Repository\ComptabiliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comptabilite")
 */

class ComptabiliteController extends AbstractController
{
    /**
     * @Route("/", name="app_comptabilite_index")
     */
    public function index(): Response
    {
        $comptabiliters=$this->getDoctrine()->getRepository(Comptabilite::class)->findAll();

        return $this->render('comptabilite/index.html.twig', [
            'comptabiliters' => $comptabiliters,
        ]);
    }


    /**
     * @Route("/new", name="app_comptabilite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ComptabiliteRepository $comptabiliteRepository): Response
    {
        $comptabilite = new Comptabilite();
        $form = $this->createForm(ComptabiliteType::class, $comptabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comptabiliteRepository->add($comptabilite);
            return $this->redirectToRoute('app_comptabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comptabilite/add.html.twig', [
            'comptabilite' => $comptabilite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_comptabilite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Comptabilite $comptabilite, ComptabiliteRepository $comptabiliteRepository): Response
    {
        $form = $this->createForm(ComptabiliteType::class, $comptabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comptabiliteRepository->add($comptabilite);
            return $this->redirectToRoute('app_comptabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comptabilite/edit.html.twig', [
            'comptabilite' => $comptabilite,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_comptabilite_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $comptabilite = $this->getDoctrine()->getRepository(Comptabilite::class)->find($id);

        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($comptabilite);
        $entityyManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('app_comptabilite_index', [], Response::HTTP_SEE_OTHER);
    }
}
