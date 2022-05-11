<?php

namespace App\Controller;

use App\Entity\Comptabilite;
use App\Entity\TypeComptabilite;
use App\Form\TypeComptabiliteType;
use App\Repository\CategorieRepository;
use App\Repository\TypeComptabiliteRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/Admin", name="app_type_comptabilite_index_admin")
     */
    public function indexAdmin(): Response
    {

        $pieChart = new PieChart();

        $typecompts=$this->getDoctrine()->getRepository(TypeComptabilite::class)->findAll();

        $data= array();
        $stat=['Les Types', '%'];
        array_push($data,$stat);

        foreach($typecompts as $tmp)
        {
            $stat=array();
            $cmp = new Comptabilite();
            $cmp = $this->getDoctrine()->getManager()->getRepository(Comptabilite::class)->findBy([
                'id_type' => $tmp
            ]);
            $total = count($cmp);
            $stat=[$tmp->getType(),$total];
            array_push($data,$stat);
        }
        $pieChart->getData()->setArrayToDataTable(
            $data
        );

        $pieChart->getOptions()->setTitle('Les Types');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);


        return $this->render('type_comptabilite/indexAdmin.html.twig', [
            'typecompts' => $typecompts,
            'piechart' => $pieChart

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
            return $this->redirectToRoute('app_type_comptabilite_index_admin', [], Response::HTTP_SEE_OTHER);
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
            return $this->redirectToRoute('app_type_comptabilite_index_admin', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_type_comptabilite_index_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/r/search_recc", name="search_recc", methods={"GET"})
     */
    public function search_rec(Request $request, NormalizerInterface $Normalizer, TypeComptabiliteRepository $comptabiliteRepository): Response
    {

        $requestString = $request->get('searchValue');
        $requestString3 = $request->get('orderid');

        $typec = $comptabiliteRepository->findTypeC($requestString, $requestString3);
        $jsoncontentc = $Normalizer->normalize($typec, 'json', ['groups' => 'posts:read']);
        $jsonc = json_encode($jsoncontentc);
        if ($jsonc == "[]") {
            return new Response(null);
        } else {
            return new Response($jsonc);
        }
    }


    public function getData()
    {
        /**
         * @var $TypeComptabilite typ[]
         */
        $list = [];
        $typerec = $this->getDoctrine()->getRepository(TypeComptabilite::class)->findAll();

        foreach ($typerec as $typ) {
            $list[] = [
                $typ->getType(),
                $typ->getMontant(),

            ];
        }
        return $list;
    }


    /**
     * @Route("/excel/export",  name="export")
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Reclamation List');

        $sheet->getCell('A1')->setValue('type');
        $sheet->getCell('B1')->setValue('montant');

        // Increase row cursor after header write
        $sheet->fromArray($this->getData(), null, 'A2', true);
        $writer = new Xlsx($spreadsheet);
        // $writer->save('ss.xlsx');
        $writer->save('TypeCompta' . date('m-d-Y_his') . '.xlsx');
        return $this->redirectToRoute('app_type_comptabilite_index_admin');
    }



    //*****MOBILE

    /**
     * @Route("/mobile/aff", name="affmobType")
     */
    public function affmobcategory(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(TypeComptabilite::class)->findAll();
        $jsonContent = $normalizer->normalize($med,'json',['typecomptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/new", name="addmobType")
     */
    public function addmobType(Request $request,NormalizerInterface $normalizer,TypeComptabiliteRepository $typeComptabiliteRepository)
    {
        $em=$this->getDoctrine()->getManager();
        $type= new TypeComptabilite();
        $type->setType($request->get('type'));
        $type->setMontant($request->get('montant'));
        $typeComptabiliteRepository->add($type);

        $jsonContent = $normalizer->normalize($type,'json',['typecomptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/mobile/edit", name="editmobType")
     */
    public function editmobType(Request $request,NormalizerInterface $normalizer)
    {   $em=$this->getDoctrine()->getManager();
        $type = $em->getRepository(TypeComptabilite::class)->find($request->get('id'));
        $type->setType($request->get('type'));
        $type->setMontant($request->get('montant'));

        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($type) {
            return $type->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($type);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/mobile/del", name="delmobType")
     */
    public function delmobType(Request $request,NormalizerInterface $normalizer)
    {           $em=$this->getDoctrine()->getManager();
        $type=$this->getDoctrine()->getRepository(TypeComptabilite::class)
            ->find($request->get('id'));
        $em->remove($type);
        $em->flush();
        $jsonContent = $normalizer->normalize($type,'json',['typecomptabilite'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

}
