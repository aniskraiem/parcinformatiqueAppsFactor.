<?php

namespace App\Controller;

use App\Entity\Demandes;
use App\Entity\Materiel;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function index(): Response
    {
        return $this->render('acceuil/all.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function countnbrm()

    {
        $materiels = $this->getDoctrine()->getRepository(Materiel::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $demande = $this->getDoctrine()->getRepository(Demandes::class)->findAll();


        return $this->render('acceuil/all.html.twig', [
            'materiels' => $materiels,
            'users' => $users,
            'demandes' => $demande

        ]);
    }

}
