<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\RelationnelRepository;
use App\Repository\UserRepository;
use App\Services\QrcodeService;
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
 * @Route("/reclamation")
 */

class ReclamationController extends AbstractController
{
    /**
     * @Route("/", name="app_reclamation_index")
     */
    public function index(): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->findBy([
            'user' => $user
        ]);
        $repones=$this->getDoctrine()->getRepository(Reponse::class)->findAll();
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'repones' => $repones,
        ]);
    }
    /**
     * @Route("/Admin", name="app_reclamation_index_admin")
     */
    public function indexAdmin(): Response
    {
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class)->findAll();

        return $this->render('reclamation/indexAdmin.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }


    /**
     * @Route("/new", name="app_reclamation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReclamationRepository $reclamationRepository,UserRepository  $userRepository,QrcodeService $qrcodeService): Response
    {
        $qrCode=null;

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
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
                $reclamation->setImage($fileName);
            }
            else
            {
                $reclamation->setImage("NoImage.png");
            }
            $reclamation->setUser($userRepository->find(1));
            $reclamation->setDateReclamation(new \DateTime());

            $reclamationRepository->add($reclamation);
            $qrCode=$qrcodeService->qrCode($reclamation->getDescription());
            $this->addFlash(
                'info',
                'Added Successfully'
            );

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/add.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_reclamation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
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
                $reclamation->setImage($fileName);
            }
            $reclamationRepository->add($reclamation);
            $this->addFlash(
                'info-edit',
                'Updated Successfully'
            );

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_reclamation_delete")
     * method=({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $entityyManager = $this->getDoctrine()->getManager();
        $entityyManager->remove($reclamation);
        $entityyManager->flush();
        $response = new Response();
        $response->send();
        $this->addFlash(
            'info-delete',
            'Deleted Successfully'
        );
        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/pdf/{id}", name="reclamation_pdf")
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

        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation/pdf.html.twig', [
            'reclamation' => $reclamation
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);



        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'paysage');

        // Render the HTML as PDF
        $dompdf->render();



        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("reclamation.pdf", [
            "Attachment" => false
        ]);
        return new Response();
    }
    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobreclamation")
     */
    public function affmobreclamation(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['reclamation'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobreclamation")
     */
    public function addmobreclamation(Request $request,NormalizerInterface $normalizer,ReclamationRepository $reclamationRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $reclamation= new Reclamation();
        $reclamation->setDescription($request->get('description'));
        $reclamation->setImage($request->get('image'));
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('iduser'));
        $reclamation->setUser($user);
        $reclamation->setDateReclamation(new \DateTime());

        $reclamationRepository->add($reclamation);

        $jsonContent = $normalizer->normalize($reclamation,'json',['reclamation'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobreclamation")
     */
    public function editmobreclamation(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();

        $reclamation = $em->getRepository(Reclamation::class)->find($request->get('id'));

        $reclamation->setDescription($request->get('description'));
        $reclamation->setImage($request->get('image'));


        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($reclamation) {
            return $reclamation->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($reclamation);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobreclamation")
     */
    public function delmobreclamation(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $reclamation=$this->getDoctrine()->getRepository(Reclamation::class)
            ->find($request->get('id'));
        $em->remove($reclamation);
        $em->flush();
        $jsonContent = $normalizer->normalize($reclamation,'json',['reclamation'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }


}
