<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/Affiche", name="affiche")
     */
    public function Affiche(UserRepository $repository){
      //  $repo=$this->getDoctrine->getRepository(User::class);
        $user=$repository->findAll();
        return $this->render('user/Affiche.html.twig', 
        [ 'user' => $user ]);

    }

    /**
     * @Route("/Supp/{id}", name="app_user_delete")
     */
    public function Delete($id, UserRepository $repository){
    
          $user=$repository->find($id);
          $em=$this->getDoctrine()->getManager();
          $em->remove($user);
          $em->flush();
          return $this->redirectToRoute('affiche');
  
      }

      /**
     * @Route("/Ajouter", name="ajouter")
     */
    public function Add(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('affiche');
          
        }
        return $this->render('user/Add.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/Update/{id}", name="app_user_edit")
     */
    public function Update($id, UserRepository $repository,Request $request){
    
        $user=$repository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('affiche');

        }
        return $this->render('user/Update.html.twig', [
            'f' => $form->createView(),
        ]);
    }

}
