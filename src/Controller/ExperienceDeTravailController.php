<?php

namespace App\Controller;

use App\Entity\ExperienceDeTravail;
use App\Form\ExperienceDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExperienceDeTravailController extends AbstractController
{
    /**
     * @Route("/experienceDeTravail", name="afficherExperienceDeTravail")
     */
    public function afficherExperienceDeTravail(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/experienceDeTravail/afficherExperienceDeTravail.html.twig', [
            'experienceDeTravails' => $this->getDoctrine()->getManager()->getRepository(ExperienceDeTravail::class)->findAll(),
        ]);
    }

    /**
     * @Route("/candidat/experienceDeTravail/ajouter", name="ajouterExperienceDeTravail")
     */
    public function ajouterExperienceDeTravail(Request $request)
    {
        $experienceDeTravail = new ExperienceDeTravail();

        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $experienceDeTravail = $form->getData();

            $experienceDeTravailRepository = $this->getDoctrine()->getManager();
            $experienceDeTravailRepository->persist($experienceDeTravail);
            $experienceDeTravailRepository->flush();

            return $this->redirectToRoute('afficherExperienceDeTravail');
        }

        return $this->render('/frontEnd/utilisateur/societe/experienceDeTravail/manipulerExperienceDeTravail.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/experienceDeTravail={idExperienceDeTravail}/modifier", name="modifierExperienceDeTravail")
     */
    public function modifierExperienceDeTravail(Request $request, $idExperienceDeTravail)
    {
        $experienceDeTravailRepository = $this->getDoctrine()->getManager();
        $experienceDeTravail = $experienceDeTravailRepository->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);

        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experienceDeTravailRepository->flush();
            return $this->redirectToRoute('afficherExperienceDeTravail');
        }

        return $this->render('/frontEnd/utilisateur/societe/experienceDeTravail/manipulerExperienceDeTravail.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/experienceDeTravail={idExperienceDeTravail}/supprimer", name="supprimerExperienceDeTravail")
     */
    public function supprimerExperienceDeTravail($idExperienceDeTravail)
    {
        $experienceDeTravailManager = $this->getDoctrine()->getManager();
        $experienceDeTravail = $experienceDeTravailManager->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);
        $experienceDeTravailManager->remove($experienceDeTravail);
        $experienceDeTravailManager->flush();
        return $this->redirectToRoute('afficherExperienceDeTravail');
    }
}
