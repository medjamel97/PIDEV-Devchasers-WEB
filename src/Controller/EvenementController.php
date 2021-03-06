<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/evenement={idEvenement}", name="afficherEvenement")
     */
    public function afficherEvenement($idSociete, $idEvenement): Response
    {
        return null;
    }

    /**
     * @Route("/evenement", name="afficherToutEvenement")
     */
    public function afficherToutEvenement(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/evenement/afficherEvenement.html.twig', [
            'evenements' => $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe={idSociete}/evenement/ajouter", name="ajouterEvenement")
     */
    public function ajouterEvenement(Request $request, $idSociete)
    {
        $evenement = new Evenement();

        $form = $this->createForm(EvenementType::class, $evenement)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $evenement = $form->getData();

            $evenementRepository = $this->getDoctrine()->getManager();
            $evenementRepository->persist($evenement);
            $evenementRepository->flush();

            return $this->redirectToRoute('afficherToutEvenement');
        }

        return $this->render('/frontEnd/utilisateur/societe/evenement/manipulerEvenement.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/evenement={idEvenement}/modifier", name="modifierEvenement")
     */
    public function modifierEvenement(Request $request, $idEvenement)
    {
        $evenementRepository = $this->getDoctrine()->getManager();
        $evenement = $evenementRepository->getRepository(Evenement::class)->find($idEvenement);

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenementRepository->flush();
            return $this->redirectToRoute('afficherToutEvenement');
        }

        return $this->render('/frontEnd/utilisateur/societe/evenement/manipulerEvenement.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/evenement={idEvenement}/supprimer", name="supprimerEvenement")
     */
    public function supprimerEvenement($idEvenement)
    {
        $evenementManager = $this->getDoctrine()->getManager();
        $evenement = $evenementManager->getRepository(Evenement::class)->find($idEvenement);
        $evenementManager->remove($evenement);
        $evenementManager->flush();
        return $this->redirectToRoute('afficherToutEvenement');
    }
}