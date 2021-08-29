<?php

namespace App\Controller;

use App\Entity\Demandes;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use datetime;

class DemandeController extends AbstractController
{
    /**
     * @Route("/demande", name="demande")
     */
    public function afficher()
    {
        $demande = $this->getDoctrine()->getRepository(Demandes::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('demande/demandes.html.twig', [
            'demandes' => $demande,
            'users'=>$user
        ]);
    }

    /**
     * @Route("/ajoutdemandes", name="ajoutdemandes")
     */
    public function ajout(Request $request): Response
    {
        if ($request->getMethod() == 'POST') {
            $demande = new Demandes();
            $demande->setLibelle($request->get('titre'));
            $demande->setStatut($request->get('statut'));
            $date = new DateTime($request->get('dateD'));
            $demande->setDate($date);
            $date2 = new DateTime($request->get('dateF'));
            $demande->setDateLimite($date2);
            $demande->setClient($request->get('client'));
            $demande->setDescription($request->get('desc'));
            $demande->setNomdemachine($request->get('nomdemachine'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demande);
            $entityManager->flush();
            return $this->redirectToRoute('demande'
            );
        }
        return $this->render('demande/ajoutdemande.html.twig'
        );
    }

    /**
     * @Route("/suppdemande/{iddemande}", name="suppdemande")
     */

    public function supprimer(Request $request, $iddemande)
    {
        $demande = $this->getDoctrine()->getRepository(Demandes::class)->find($iddemande);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($demande);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('demande');
    }

    /**
     * @Route("/modifdemandes/{iddemande}", name="modifdemandes")
     */
    public function modifier(Request $request, $iddemande): Response
    {
        $demande = $this->getDoctrine()->getRepository(Demandes::class)->find($iddemande);

        if ($request->getMethod() == 'POST') {
            $demande->setLibelle($request->get('titre'));
            $demande->setStatut($request->get('statut'));
            $date = new DateTime($request->get('dateD'));
            $demande->setDate($date);
            $date2 = new DateTime($request->get('dateF'));
            $demande->setDateLimite($date2);
            $demande->setClient($request->get('client'));
            $demande->setDescription($request->get('desc'));
            $demande->setNomdemachine($request->get('nomdemachine'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('demande'
            );
        }
        return $this->render('demande/modifdemandes.html.twig',[
            'demandes' => $demande
            ]);
    }

    /**
     * @Route("/statutdemandes/{iddemande}", name="statutdemandes")
     */
    public function changer($iddemande, Request $request)
    {


        $demande = $this->getDoctrine()->getRepository(Demandes::class)->find($iddemande);


        $demande->setStatut('Fermé');

        $em = $this->getDoctrine()->getManager();
        $em->persist($demande);
        $em->flush();

        return $this->redirectToRoute('demande'
        );
    }
}
