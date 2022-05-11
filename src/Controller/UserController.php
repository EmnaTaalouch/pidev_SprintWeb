<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\UserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")!h
     *
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/adminlist",name="adminlist")
     */
    public function list(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(User::class)->findAll();

        $admin = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render("admin/listaffiche.html.twig",
            array('tabadmin'=>$admin));
    }
    /**
     * @Route("/addu",name="addUser")
     */
    public function add(Request $request, UserPasswordEncoderInterface $userPasswordEncoderInterface){
        $Admin= new User();
        $form= $this->createForm(UserType::class,$Admin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $Admin->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                    $Admin,
                    $form->get('password')->getData()
                )
            );
            $Admin->setState(0);
            $Admin->setRole('admin');
            $em = $this->getDoctrine()->getManager();
            $em->persist($Admin);
            $em->flush();
            $this->addFlash('message', 'Ajout avec succée !');
            return $this->redirectToRoute("addUser");

        }
        return $this->render("admin/listadmin.html.twig",array("formuser"=>$form->createView()));
    }



    /**
     * @Route("/update/{id}",name="update", methods={"GET","POST"})
     */
    public function update(Request $request,$id, UserPasswordEncoderInterface $userPasswordEncoderInterface){
        $admin= $this->getDoctrine()->getRepository(user::class)->find($id);
        $form= $this->createForm(UserType::class,$admin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $admin->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                    $admin,
                    $form->get('password')->getData()
                )
            );
            $admin->setRole("admin");
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("adminlist");
        }
        return $this->render("admin/update.html.twig",  array("formuser"=>$form->createView()));
    }

    /**
     * @Route("/profile",name="profile")
     */
    public function profile(Request $request,UserPasswordEncoderInterface $userPasswordEncoderInterface){
        $admin = $this->getUser();
        $form= $this->createForm(ProfileType::class,$admin);
        $form->handleRequest($request);
        $admin->setPassword($admin->getPassword());
        if($form->isSubmitted() && $form->isValid()){
            if($form->get('password') == ""){
                $admin->setPassword($admin->getPassword());
            }else{
                $admin->setPassword(
                    $userPasswordEncoderInterface->encodePassword(
                        $admin,
                        $form->get('password')->getData()
                    )
                );
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('message','profile mis a jour avec succès');
            return $this->redirectToRoute("profile");
        }
        return $this->render("admin/profile.html.twig",  array("formprofile"=>$form->createView()));
    }
    /**
     * @Route("/remove/{id}",name="remove")
     */
    public function delete($id){
        $admin= $this->getDoctrine()->getRepository(user::class)->find($id);
        $em= $this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();
        $this->addFlash('message', 'Suppression avec succée !');
        return $this->redirectToRoute("adminlist");

    }

    /**
     * @Route("/EnableUser/{id}", name="enableUser")
     * @param $id
     * @return
     */
    public function EnableUser($id)
    {
        $admin = $this->getDoctrine()->getRepository(User::class)->find($id);
        $admin->setState(0);
        $entityManager = $this->getDoctrine()->getManager();
        $admin->setIsVerified(1);
        $entityManager->flush();
        $this->addFlash("success","Compte Activé !!") ;
        return $this->redirectToRoute('adminlist', ['id' => $admin->getId()]);
    }

    /**
     * @Route("/DiableUser/{id}", name="diableUser")
     * @param $id
     * @return
     */
    public function DiableUser($id)
    {
        $admin = $this->getDoctrine()->getRepository(User::class)->find($id);
        $admin->setState(1);

        $entityManager = $this->getDoctrine()->getManager();
        $admin->setIsVerified(0);
        $entityManager->flush();
        $this->addFlash("success","Compte déactivé !!") ;
        return $this->redirectToRoute('adminlist', ['id' => $admin->getId()]);
    }


    /**
     * @Route("/search", name="search_user", requirements={"id":"\d+"})

     */
     public function searchGuides(Request $request, NormalizerInterface $Normalizer)
     {
         $repository = $this->getDoctrine()->getRepository(User::class);
         $requestString = $request->get('searchValue');
         $user = $repository->findUserByNom($requestString);
         $jsonContent = $Normalizer->normalize($user, 'json',[]);

         return new Response(json_encode($jsonContent));
     }

}
