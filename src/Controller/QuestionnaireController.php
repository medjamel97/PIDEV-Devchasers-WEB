<?php

namespace App\Controller;

use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireController extends AbstractController
{
    /**
     * @Route("/societe/mission/questionnaire", name="afficherQuestionnaire")
     */
    public function afficherQuestionnaire(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/questionnaire/afficherQuestionnaire.html.twig', [
            'questionnaires' => $this->getDoctrine()->getManager()->getRepository(Questionnaire::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe={}/mission={}/questionnaire={}", name="afficherToutQuestionnaire")
     */
    public function afficherToutQuestionnaire(): Response
    {
        return null;
    }

    /**
     * @Route("/societe/mission/questionnaire/ajouter", name="ajouterQuestionnaire")
     */
    public function ajouterQuestionnaire(Request $request)
    {
        $questionnaire = new Questionnaire();

        $form = $this->createForm(QuestionnaireType::class, $questionnaire)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questionnaire = $form->getData();

            $questionnaireRepository = $this->getDoctrine()->getManager();
            $questionnaireRepository->persist($questionnaire);
            $questionnaireRepository->flush();

            return $this->redirectToRoute('afficherQuestionnaire');
        }

        return $this->render('/frontEnd/utilisateur/societe/questionnaire/manipulerQuestionnaire.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission/questionnaire={idQuestionnaire}/modifier", name="modifierQuestionnaire")
     */
    public function modifierQuestionnaire(Request $request, $idQuestionnaire)
    {
        $questionnaireRepository = $this->getDoctrine()->getManager();
        $questionnaire = $questionnaireRepository->getRepository(Questionnaire::class)->find($idQuestionnaire);

        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaireRepository->flush();
            return $this->redirectToRoute('afficherQuestionnaire');
        }

        return $this->render('/frontEnd/utilisateur/societe/questionnaire/manipulerQuestionnaire.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission/questionnaire={idQuestionnaire}/supprimer", name="supprimerQuestionnaire")
     */
    public function supprimerQuestionnaire($idQuestionnaire)
    {
        $questionnaireManager = $this->getDoctrine()->getManager();
        $questionnaire = $questionnaireManager->getRepository(Questionnaire::class)->find($idQuestionnaire);
        $questionnaireManager->remove($questionnaire);
        $questionnaireManager->flush();
        return $this->redirectToRoute('afficherQuestionnaire');
    }
}
