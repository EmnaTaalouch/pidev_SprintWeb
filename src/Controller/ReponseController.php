<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Repository\TypeComptabiliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobrep")
     */
    public function affmobrep(Request $request,NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Reponse::class)->findBy([
            'reclamations' => $request->get('id')
        ]);
        $jsonContent = $normalizer->normalize($med,'json',['reponse'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    /**
     * @Route("/mobile/affAll", name="affallmobrep")
     */
    public function affallmobrep(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Reponse::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['reponse'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobRep")
     */
    public function addmobRep(Request $request,NormalizerInterface $normalizer,ReponseRepository $reponseRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $rep= new Reponse();
        $rep->setText($request->get('text'));
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->find($request->get('idrec'));


        $rep->setReclamations($reclamations);
        $reponseRepository->add($rep);

        $jsonContent = $normalizer->normalize($rep,'json',['reponse'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobRep")
     */
    public function editmobRep(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();
        $rep = $em->getRepository(Reponse::class)->find($request->get('id'));
        $rep->setText($request->get('text'));

        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($rep) {
            return $rep->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($rep);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobRep")
     */
    public function delmobRep(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository(Reponse::class)
            ->find($request->get('id'));
        $em->remove($rep);
        $em->flush();
        $jsonContent = $normalizer->normalize($rep,'json',['reponse'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

}
