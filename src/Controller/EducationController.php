<?php

namespace App\Controller;

use App\Entity\Education;
use App\Form\EducationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EducationController extends AbstractController
{
    /**
     * @Route("/candidat={}/education={}", name="afficherEducation")
     */
    public function afficherEducation(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/education/afficherEducation.html.twig', [
            'educations' => $this->getDoctrine()->getManager()->getRepository(Education::class)->findAll(),
        ]);
    }

    /**
     * @Route("/education", name="afficherToutEducation")
     */
    public function afficherToutEducation(): Response
    {
        return null;
    }

    /**
     * @Route("/candidat/education/ajouter", name="ajouterEducation")
     */
    public function ajouterEducation(Request $request)
    {
        $education = new Education();

        $form = $this->createForm(EducationType::class, $education)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $education = $form->getData();

            $educationRepository = $this->getDoctrine()->getManager();
            $educationRepository->persist($education);
            $educationRepository->flush();

            return $this->redirectToRoute('afficherEducation');
        }

        return $this->render('/frontEnd/utilisateur/societe/education/manipulerEducation.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/education={idEducation}/modifier", name="modifierEducation")
     */
    public function modifierEducation(Request $request, $idEducation)
    {
        $educationRepository = $this->getDoctrine()->getManager();
        $education = $educationRepository->getRepository(Education::class)->find($idEducation);

        $form = $this->createForm(EducationType::class, $education);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $educationRepository->flush();
            return $this->redirectToRoute('afficherEducation');
        }

        return $this->render('/frontEnd/utilisateur/societe/education/manipulerEducation.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/education={idEducation}/supprimer", name="supprimerEducation")
     */
    public function supprimerEducation($idEducation)
    {
        $educationManager = $this->getDoctrine()->getManager();
        $education = $educationManager->getRepository(Education::class)->find($idEducation);
        $educationManager->remove($education);
        $educationManager->flush();
        return $this->redirectToRoute('afficherEducation');
    }
}
