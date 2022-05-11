<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
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
 * @Route("/categorie")
 */

class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="app_categorie_index")
     */
    public function index(): Response
    {
        $categories=$this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/indexAdmin.html.twig', [
            'categories' => $categories,
        ]);
    }


    /**
     * @Route("/new", name="app_categorie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categorieRepository->add($categorie);
            $this->addFlash(
                'info',
                'Added Successfully'
            );
            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/add.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_categorie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'info-edit',
                'Updated Successfully'
            );

            $categorieRepository->add($categorie);
            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_categorie_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($categorie);
        $entityyManager->flush();

        $response = new Response();
        $response->send();
        $this->addFlash(
            'info-delete',
            'Deleted Successfully'
        );


        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }

    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobcategory")
     */
    public function affmobcategory(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['categorie'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobCategory")
     */
    public function addmobcategorie(Request $request,NormalizerInterface $normalizer,CategorieRepository $categorieRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $categorie= new Categorie();
        $categorie->setRole($request->get('role'));
        $categorieRepository->add($categorie);

        $jsonContent = $normalizer->normalize($categorie,'json',['categorie'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobCategory")
     */
    public function editmobcategorie(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();
        $categorie = $em->getRepository(Categorie::class)->find($request->get('id'));
        $categorie->setRole($request->get('role'));

        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($categorie) {
            return $categorie->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($categorie);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobCategory")
     */
    public function delmobcategorie(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $categorie=$this->getDoctrine()->getRepository(Categorie::class)
            ->find($request->get('id'));
        $em->remove($categorie);
        $em->flush();
        $jsonContent = $normalizer->normalize($categorie,'json',['categorie'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

}
