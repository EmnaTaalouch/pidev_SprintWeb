<?php

namespace App\Controller;

use App\Entity\TypeComptabilite;
use App\Form\TypeComptabiliteType;
use App\Repository\TypeComptabiliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/typecomptabilite")
 */

class TypeComptabiliteController extends AbstractController
{
    /**
     * @Route("/", name="app_type_comptabilite_index")
     */
    public function index(): Response
    {
        $typecompts=$this->getDoctrine()->getRepository(TypeComptabilite::class)->findAll();
        return $this->render('type_comptabilite/index.html.twig', [
            'typecompts' => $typecompts,
        ]);
    }


    /**
         * @Route("/new", name="app_type_comptabilite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TypeComptabiliteRepository $typeComptabiliteRepository): Response
    {
        $typeComptabilite = new TypeComptabilite();
        $form = $this->createForm(TypeComptabiliteType::class, $typeComptabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $typeComptabiliteRepository->add($typeComptabilite);
            return $this->redirectToRoute('app_type_comptabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_comptabilite/add.html.twig', [
            'typeComptabilite' => $typeComptabilite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_type_comptabilite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TypeComptabilite $typeComptabilite, TypeComptabiliteRepository $typeComptabiliteRepository): Response
    {
        $form = $this->createForm(TypeComptabiliteType::class, $typeComptabilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeComptabiliteRepository->add($typeComptabilite);
            return $this->redirectToRoute('app_type_comptabilite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_comptabilite/edit.html.twig', [
            'typeComptabilite' => $typeComptabilite,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_type_comptabilite_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $typeComptabilite = $this->getDoctrine()->getRepository(TypeComptabilite::class)->find($id);

        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($typeComptabilite);
        $entityyManager->flush();
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('app_type_comptabilite_index', [], Response::HTTP_SEE_OTHER);
    }
}
