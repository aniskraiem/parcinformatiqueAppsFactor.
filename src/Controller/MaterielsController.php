<?php

namespace App\Controller;
use App\Entity\Materiel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\MaterielFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use datetime;
use Unirest;
use Unirest\Exception;
use JJG\Ping;
class MaterielsController extends AbstractController
{
    /**
     * @Route("/materiels", name="materiels")
     */
    public function afficher()
    {
        $materiels = $this->getDoctrine()->getRepository(Materiel::class)->findAll();

        foreach ($materiels as $machine) {


            try {

                $ping = new \JJG\Ping($machine->getAddIp());
                $latency = $ping->ping();
                if ($latency == true) {
                    $statut = "Activer";
                } else {
                    $statut = "Desactiver";
                }

            } catch (Exception $exception) {
                $statut = "Desactiver";
            }
            $machine->setStatut($statut);
        }
        return $this->render('materiels/machines.html.twig', [
            'materiels' => $materiels ]);
    }

    /**
     * @Route("/detailsmateriels/{idmachine}", name="detailsmateriels")
     */
        public function details($idmachine)
    {
        $materiels = $this->getDoctrine()->getRepository(Materiel::class)->find($idmachine);

        return $this->render('materiels/detailsmateriels.html.twig', [
            'materiels' => $materiels ]);

    }


    /**
     * @Route("/ajoutmateriels", name="ajoutmateriels")
     */
    public function ajout(Request $request): Response
    {
        if($request->getMethod()=='POST') {
            $materiel = new Materiel();
            $materiel->setType($request->get('type'));
            $materiel->setModele($request->get('caracts'));
            $materiel->setAddMac($request->get('adressemac'));
            $materiel->setAddIp($request->get('adresseip'));
            $dateTime = new DateTime($request->get('dateaffectation'));
            $materiel->setDateAcc($dateTime);
            $dateTime2 = new DateTime($request->get('dateexp'));
            $materiel->setDateFin($dateTime2);
            $materiel->setApps($request->get('apps'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materiel);
            $entityManager->flush();
            return $this->redirectToRoute('materiels'
            );
        }
        return $this->render('materiels/ajout.html.twig'
        );
    }
    /**
     * @Route("/modifmateriels/{idmachine}", name="modifmateriels")
     */
    public function modifier(Request $request, $idmachine): Response
    {


        $materiel=$this->getDoctrine()->getRepository(Materiel::class)->find($idmachine);

        if($request->getMethod()=='POST') {
            $materiel->setType($request->get('type'));
            $materiel->setModele($request->get('caracts'));
            $materiel->setAddMac($request->get('adressemac'));
            $materiel->setAddIp($request->get('adresseip'));
            $dateTime = new DateTime($request->get('dateaffectation'));
            $materiel->setDateAcc($dateTime);
            $dateTime2 = new DateTime($request->get('dateexp'));
            $materiel->setDateFin($dateTime2);
            $materiel->setApps($request->get('apps'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('materiels'
            );
        }
        return $this->render('materiels/modifier.html.twig',
            [
                'materiels' => $materiel ]);


    }
    /**
     * @Route("/suppmateriel/{idmachine}", name="suppmateriel")
     */

    public function supprimer(Request $request, $idmachine) {
        $materiel=$this->getDoctrine()->getRepository(Materiel::class)->find($idmachine);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($materiel);
        $entityManager->flush();

        $response = new Response();
        $response->send();

        return $this->redirectToRoute('materiels');
    }
}
