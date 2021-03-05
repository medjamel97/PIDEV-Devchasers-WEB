<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Form\CompetenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetenceController extends AbstractController
{
    /**
     * @Route("/candidat={}/competence={}", name="afficherCompetence")
     */
    public function afficherCompetence(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/competence/afficherCompetence.html.twig', [
            'competences' => $this->getDoctrine()->getManager()->getRepository(Competence::class)->findAll(),
        ]);
    }

    /**
     * @Route("competence", name="afficherToutCompetence")
     */
    public function afficherToutCompetence(): Response
    {
        return null;
    }

    /**
     * @Route("/candidat/competence/ajouter", name="ajouterCompetence")
     */
    public function ajouterCompetence(Request $request)
    {
        $competence = new Competence();

        $form = $this->createForm(CompetenceType::class, $competence)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $competence = $form->getData();

            $competenceRepository = $this->getDoctrine()->getManager();
            $competenceRepository->persist($competence);
            $competenceRepository->flush();

            return $this->redirectToRoute('afficherCompetence');
        }

        return $this->render('/frontEnd/utilisateur/societe/competence/manipulerCompetence.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/competence={idCompetence}/modifier", name="modifierCompetence")
     */
    public function modifierCompetence(Request $request, $idCompetence)
    {
        $competenceRepository = $this->getDoctrine()->getManager();
        $competence = $competenceRepository->getRepository(Competence::class)->find($idCompetence);

        $form = $this->createForm(CompetenceType::class, $competence);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competenceRepository->flush();
            return $this->redirectToRoute('afficherCompetence');
        }

        return $this->render('/frontEnd/utilisateur/societe/competence/manipulerCompetence.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/competence={idCompetence}/supprimer", name="supprimerCompetence")
     */
    public function supprimerCompetence($idCompetence)
    {
        $competenceManager = $this->getDoctrine()->getManager();
        $competence = $competenceManager->getRepository(Competence::class)->find($idCompetence);
        $competenceManager->remove($competence);
        $competenceManager->flush();
        return $this->redirectToRoute('afficherCompetence');
    }
}
