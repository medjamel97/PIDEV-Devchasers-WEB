<?php

namespace App\Controller\back_end;

use App\Entity\Formation;
use App\Form\FormationType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
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

        // Paginate the results of the query
        $paginator = $this->get('knp_paginator');
        $formations = $paginator->paginate(
        // Doctrine Query, not results
            $formations,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('back_end/societe/formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * @Route("formation/{idFormation}", name="afficher_formation")
     */
    public function afficherFormation($idFormation)
    {
        return $this->render('back_end/societe/formation/afficher.html.twig', [
            'formation' => $this->getDoctrine()->getRepository(Formation::class)->find($idFormation),
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

        return $this->render('back_end/societe/formation/indexR.html.twig', array(
            'formations' => $formations
        ));
    }

    /**
     * @Route("formation/ajouter", name="ajouter_formation")
     */
    public function ajouterFormation(Request $request)
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('');
        }

        return $this->render('back_end/societe/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("formation/{idFormation}/modifier", name="modifier_formation")
     */
    public function modifierFormation(Request $request, $idFormation)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($idFormation);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('');
        }

        return $this->render('back_end/societe/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("formation/{idFormation}/supprimer", name="supprimer_formation")
     */
    public function supprimerFormation(Request $request, $idFormation)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($idFormation);
        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('');
    }
}
