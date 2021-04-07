<?php

namespace App\Controller\front_end;

use App\Entity\Formation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends Controller
{
    /**
     * @Route("formation", name="afficher_tout_formation")
     */
    public function afficherToutFormation(Request $request)
    {
        $formations = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        $paginator = $this->get('knp_paginator');
        $formations = $paginator->paginate(
            $formations,
            $request->query->getInt('page', 1), 5);

        return $this->render('front_end/societe/formation/indexFront.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * @Route("formation/recherche", name="recherche_formation")
     */
    public function rechercheFormation(Request $request)
    {
        $formation = $request->get('formation');
        $em = $this->getDoctrine()->getManager();
        if ($formation == "") {
            $formations = $em->getRepository(Formation::class)->findAll();
        } else {
            $formations = $em->getRepository(Formation::class)->findBy(
                ['nom' => $formation]
            );
        }

        return new Response(null);
    }
}
