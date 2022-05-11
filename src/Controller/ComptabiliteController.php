<?php

namespace App\Controller;

use App\Entity\Comptabilite;
use App\Entity\TypeComptabilite;
use App\Form\ComptabiliteType;
use App\Repository\ComptabiliteRepository;
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

        foreach ($comptabiliters as $comptabiliter){
            $datec[] = [
                'id' => $comptabiliter->getId(),
                'start' => $comptabiliter->getDate()->format('Y-m-d H:i:s'),
                'title' => $comptabiliter->getDescription(),
            ];
        }

        $data = json_encode($datec);
        return $this->render('comptabilite/index.html.twig', [
            'comptabiliters' => $comptabiliters,
            'data' => $data,

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

    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobCompta")
     */
    public function affmobCompta(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Comptabilite::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['comptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobCompta")
     */
    public function addmobCompta(Request $request,NormalizerInterface $normalizer,ComptabiliteRepository $comptabiliteRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $compta= new Comptabilite();
        $compta->setLibelle($request->get('libelle'));
        $compta->setDescription($request->get('description'));
        $typecompta = $this->getDoctrine()->getRepository(TypeComptabilite::class)->findOneBy([
            'type' =>  $request->get('type')
        ]);
        $compta->setIdType($typecompta);

        $rest=substr($request->get('datec'), 0, 20);
        $rest1=substr($request->get('datec'), 30, 34);
        $res=$rest.$rest1;
        try {
            $date = new \DateTime($res);
            $compta->setDate($date);
        } catch (\Exception $e) {

        }
        $comptabiliteRepository->add($compta);


        $jsonContent = $normalizer->normalize($compta,'json',['comptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobCompta")
     */
    public function editmobCompta(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();
        $compta = $em->getRepository(Comptabilite::class)->find($request->get('id'));
        $compta->setLibelle($request->get('libelle'));
        $compta->setDescription($request->get('description'));
        $typecompta = $this->getDoctrine()->getRepository(TypeComptabilite::class)->findOneBy([
            'type' =>  $request->get('type')
        ]);
        $compta->setIdType($typecompta);

        $rest=substr($request->get('datec'), 0, 20);
        $rest1=substr($request->get('datec'), 30, 34);
        $res=$rest.$rest1;
        try {
            $date = new \DateTime($res);
            $compta->setDate($date);
        } catch (\Exception $e) {

        }


        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($compta) {
            return $compta->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($compta);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobCompta")
     */
    public function delmobCompta(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $type=$this->getDoctrine()->getRepository(Comptabilite::class)
            ->find($request->get('id'));
        $em->remove($type);
        $em->flush();
        $jsonContent = $normalizer->normalize($type,'json',['comptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

}
