<?php

namespace App\Controller;

use App\Entity\Relationnel;
use App\Form\RelationnelType;
use App\Repository\RelationnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/relationnel")
 */

class RelationnelController extends AbstractController
{
    /**
     * @Route("/", name="app_relationnel_index")
     */
    public function index(): Response
    {
        $relationnels=$this->getDoctrine()->getRepository(Relationnel::class)->findAll();
        return $this->render('relationnel/indexAdmin.html.twig', [
            'relationnels' => $relationnels,
        ]);
    }


    /**
     * @Route("/new", name="app_relationnel_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RelationnelRepository $relationnelRepository): Response
    {
        $relationnel = new Relationnel();
        $form = $this->createForm(RelationnelType::class, $relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e){

            }
            $relationnel->setImage($fileName);
            $relationnelRepository->add($relationnel);
            return $this->redirectToRoute('app_relationnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relationnel/add.html.twig', [
            'relationnel' => $relationnel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_relationnel_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Relationnel $relationnel, RelationnelRepository $relationnelRepository): Response
    {
        $form = $this->createForm(RelationnelType::class, $relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relationnelRepository->add($relationnel);
            return $this->redirectToRoute('app_relationnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relationnel/edit.html.twig', [
            'relationnel' => $relationnel,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_relationnel_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $relationnel = $this->getDoctrine()->getRepository(Relationnel::class)->find($id);

        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($relationnel);
        $entityyManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('app_relationnel_index', [], Response::HTTP_SEE_OTHER);
    }}
