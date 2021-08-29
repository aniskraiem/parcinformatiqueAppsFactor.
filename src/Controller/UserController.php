<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function afficher()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('user/administrations.html.twig', [
            'users' => $users
        ]);
    }


    /**
     * @Route("/ajoutuser", name="ajoutuser")
     */
    public function ajout(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if($request->getMethod()=='POST') {
            $user = new User();
            $user->setNom($request->get('nom'));
            $user->setPrenom($request->get('prenom'));
            $user->setEmail($request->get('email'));
            $user->setRoles($request->get('role'));
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user, "appsfactor1"

                )
            );
            $user->setUsername($request->get('pseudo'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user'
            );
        }
        return $this->render('user/ajouteruser.html.twig'
        );
    }
    /**
     * @Route("/modifuser/{iduser}", name="modifuser")
     */
    public function modifier(Request $request, $iduser,UserPasswordEncoderInterface $passwordEncoder): Response
    {


        $user=$this->getDoctrine()->getRepository(User::class)->find($iduser);


            if($request->getMethod()=='POST') {
                $user->setNom($request->get('nom'));
                $user->setPrenom($request->get('prenom'));
                $user->setEmail($request->get('email'));
                $user->setRole($request->get('role'));
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user, "appsfactor1"

                    )
                );                     $user->setUsername($request->get('pseudo'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('user'
                );
        }
        return $this->render('user/modifieruser.html.twig',[
            'users'=>$user

            ]);
    }
    /**
     * @Route("/suppuser/{iduser}", name="suppuser")
     */

    public function supprimer(Request $request, $iduser) {
        $user=$this->getDoctrine()->getRepository(User::class)->find($iduser);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('user');
    }
}
