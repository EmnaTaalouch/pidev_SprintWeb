<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Relationnel;
use App\Form\RatingCType;
use App\Form\RelationnelType;
use App\Repository\RelationnelRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        return $this->render('relationnel/index.html.twig', [
            'relationnels' => $relationnels,
        ]);
    }
    /**
     * @Route("/Admin", name="app_relationnel_index_admin")
     */
    public function indexAdmin(): Response
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
            if($file)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){

                }
                $relationnel->setImage($fileName);
            }
            else
            {
                $relationnel->setImage("NoImage.png");
            }

            $relationnel->setRating(0);


            $relationnelRepository->add($relationnel);
            return $this->redirectToRoute('app_relationnel_index_admin', [], Response::HTTP_SEE_OTHER);
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
            $file = $form->get('image')->getData();
            if($file)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){

                }
                $relationnel->setImage($fileName);
            }
            $relationnelRepository->add($relationnel);
            return $this->redirectToRoute('app_relationnel_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relationnel/edit.html.twig', [
            'relationnel' => $relationnel,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/editRating/{id}", name="app_relationnel_edit_rating", methods={"GET", "POST"})
     */
    public function editRating(Request $request, Relationnel $relationnel, RelationnelRepository $relationnelRepository): Response
    {
        $form = $this->createForm(RatingCType::class, $relationnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relationnelRepository->add($relationnel);
            return $this->redirectToRoute('app_relationnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relationnel/editRating.html.twig', [
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

        return $this->redirectToRoute('app_relationnel_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/r/search_relation", name="search_rela", methods={"GET"})
     */
    public function search_reccc(Request $request, NormalizerInterface $Normalizer, RelationnelRepository $relationnelRepository): Response
    {

        $requestString = $request->get('searchValue');
        $requestString3 = $request->get('orderid');

        $relationnel = $relationnelRepository->findRelactionnel($requestString, $requestString3);
        $jsoncontentc = $Normalizer->normalize($relationnel, 'json', ['groups' => 'posts:read']);
        $jsonc = json_encode($jsoncontentc);
        if ($jsonc == "[]") {
            return new Response(null);
        } else {
            return new Response($jsonc);
        }
    }

    /**
     * @Route("/pdf/{id}", name="relationnel_pdf")
     */
    public function PDF(int $id)
    {
        //on definit les option du pdf
        $pdfOptions = new Options();
        //police par defaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $relationnel = $this->getDoctrine()->getRepository(Relationnel::class)->find($id);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('relationnel/pdf.html.twig', [
            'relationnel' => $relationnel
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);



        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'paysage');

        // Render the HTML as PDF
        $dompdf->render();



        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("relationnel.pdf", [
            "Attachment" => false
        ]);
        return new Response();
    }


    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobrelationnel")
     */
    public function affmobcategory(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Relationnel::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['relationnel'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobrelationnel")
     */
    public function addmobcategorie(Request $request,NormalizerInterface $normalizer,RelationnelRepository $relationnelRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $relationnel= new Relationnel();
        $relationnel->setNom($request->get('nom'));
        $relationnel->setDescription($request->get('description'));
        $relationnel->setImage($request->get('image'));
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findOneBy([
            'role' =>  $request->get('role')
        ]);
        $relationnel->setCategorie($categorie);
        $relationnel->setRating(0);
        $relationnelRepository->add($relationnel);

        $jsonContent = $normalizer->normalize($relationnel,'json',['relationnel'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobrelationnel")
     */
    public function editmobcategorie(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();
        $relationnel= new Relationnel();

        $relationnel = $em->getRepository(Relationnel::class)->find($request->get('id'));

        $relationnel->setNom($request->get('nom'));
        $relationnel->setDescription($request->get('description'));
        $relationnel->setImage($request->get('image'));
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findOneBy([
            'role' =>  $request->get('role')
        ]);
        $relationnel->setCategorie($categorie);


        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($relationnel) {
            return $relationnel->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($relationnel);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobrelationnel")
     */
    public function delmobcategorie(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $relationnel=$this->getDoctrine()->getRepository(Relationnel::class)
            ->find($request->get('id'));
        $em->remove($relationnel);
        $em->flush();
        $jsonContent = $normalizer->normalize($relationnel,'json',['relationnel'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }


}
