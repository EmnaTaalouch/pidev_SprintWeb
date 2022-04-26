<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use App\Services\QrcodeService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $dmy = $reclamation->getDateReclamation()->format('d-m-Y');

            $reclamationRepository->add($reclamation);
            $qrCode=$qrcodeService->qrCode($reclamation->getDescription()." ".$dmy);
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
}
