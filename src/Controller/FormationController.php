<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    /**
     * @Route("/societe/formation", name="afficherFormation")
     */
    public function afficherFormation(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/formation/afficherFormation.html.twig', [
            'formations' => $this->getDoctrine()->getManager()->getRepository(Formation::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe={}/formation={}", name="afficherToutFormation")
     */
    public function afficherToutFormation(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/formation/afficherFormation.html.twig', [
            'formations' => $this->getDoctrine()->getManager()->getRepository(Formation::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe/formation/ajouter", name="ajouterFormation")
     */
    public function ajouterFormation(Request $request)
    {
        $formation = new Formation();

        $form = $this->createForm(FormationType::class, $formation)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formation = $form->getData();

            $formationRepository = $this->getDoctrine()->getManager();
            $formationRepository->persist($formation);
            $formationRepository->flush();

            return $this->redirectToRoute('afficherFormation');
        }

        return $this->render('/frontEnd/utilisateur/societe/formation/manipulerFormation.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/formation={idFormation}/modifier", name="modifierFormation")
     */
    public function modifierFormation(Request $request, $idFormation)
    {
        $formationRepository = $this->getDoctrine()->getManager();
        $formation = $formationRepository->getRepository(Formation::class)->find($idFormation);

        $form = $this->createForm(FormationType::class, $formation);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->flush();
            return $this->redirectToRoute('afficherFormation');
        }

        return $this->render('/frontEnd/utilisateur/societe/formation/manipulerFormation.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/formation={idFormation}/supprimer", name="supprimerFormation")
     */
    public function supprimerFormation($idFormation)
    {
        $formationManager = $this->getDoctrine()->getManager();
        $formation = $formationManager->getRepository(Formation::class)->find($idFormation);
        $formationManager->remove($formation);
        $formationManager->flush();
        return $this->redirectToRoute('afficherFormation');
    }
}