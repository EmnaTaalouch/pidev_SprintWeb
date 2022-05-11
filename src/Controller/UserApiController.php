<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserApiController extends AbstractController
{

    /**
     * @Route("user/signup", name="app_signup", methods={"GET","POST"})
     */
    public function  signupAction(Request  $request, UserPasswordEncoderInterface $passwordEncoder) {
        $nom = $request->query->get("nom");
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        //control al email lazm @
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid.");
        }
        $user = new User();
        $user->setNom($nom);
        $user->setEmail($email);
        $pass = $passwordEncoder->encodePassword(
            $user,
            $password
        );
        $user->setPassword($pass);
        $user->setIsVerified(true);//par défaut user lazm ykoun enabled.

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("success",200);//200 ya3ni http result ta3 server OK
        }catch (\Exception $ex) {
            return new Response("execption ".$ex->getMessage());
        }
    }

    /**
     * @Route("/user/list", name="user_json", methods={"GET"})
     */
    public function uberjson(UserRepository $uberRepository,SerializerInterface $serializerInterface  ):response
    {
        $user = $uberRepository->findAll();
        $jsonContent= $serializerInterface->serialize($user,'json',['groups'=> 'user']  );
        return new Response($jsonContent);
    }


    /**
     * @Route("user/signin", name="app_signin", methods={"GET","POST"})
     */

    public function signinAction(Request $request) {
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);//bch nlawj ala user b username ta3o fi base s'il existe njibo
        //ken l9ito f base
        if($user){
            //lazm n9arn password zeda madamo crypté nesta3mlo password_verify
            if(password_verify($password,$user->getPassword())) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else {
                return new Response("passowrd not found");
            }
        }
        else {
            return new Response("failed");//ya3ni username/pass mch s7a7

        }
    }


    /**
     * @Route("user/ediUser", name="app_gestion_profile")
     */

    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $id = $request->get("id");//kima query->get wala get directement c la meme chose
        $password = $request->query->get("password");
        $email = $request->query->get("email");
        $em=$this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        //bon l modification bch na3mlouha bel image ya3ni kif tbadl profile ta3ik tzid image
        if($request->files->get("picture")!= null) {

            $file = $request->files->get("picture");//njib image fi url
            $fileName = $file->getClientOriginalName();//nom ta3ha

            //taw na5ouha w n7otaha fi dossier upload ely tet7t fih les images en principe te7t public folder
            $file->move(
                $fileName
            );
            $user->setPicture($fileName);
        }


        $user->setPassword(
            $passwordEncoder->encodePassword(
                $email,
                $password
            )
        );

        $user->setEmail($email);
        $user->setIsVerified(true);//par défaut user lazm ykoun enabled.



        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("success",200);//200 ya3ni http result ta3 server OK
        }catch (\Exception $ex) {
            return new Response("fail ".$ex->getMessage());
        }

    }
}
