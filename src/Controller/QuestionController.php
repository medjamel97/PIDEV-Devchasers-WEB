<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/mission={idMission}/questionnaire/question", name="afficherQuestion")
     */
    public function afficherQuestion(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/question/afficherQuestion.html.twig', [
            'questions' => $this->getDoctrine()->getManager()->getRepository(Question::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe={}/mission={}/questionnaire={}/question={}", name="afficherToutQuestion")
     */
    public function afficherToutQuestion(): Response
    {
        return null;
    }

    /**
     * @Route("/societe/mission/questionnaire/question/ajouter", name="ajouterQuestion")
     */
    public function ajouterQuestion(Request $request)
    {
        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $question = $form->getData();

            $questionRepository = $this->getDoctrine()->getManager();
            $questionRepository->persist($question);
            $questionRepository->flush();

            return $this->redirectToRoute('afficherQuestion');
        }

        return $this->render('/frontEnd/utilisateur/societe/question/manipulerQuestion.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission/questionnaire/question={idQuestion}/modifier", name="modifierQuestion")
     */
    public function modifierQuestion(Request $request, $idQuestion)
    {
        $questionRepository = $this->getDoctrine()->getManager();
        $question = $questionRepository->getRepository(Question::class)->find($idQuestion);

        $form = $this->createForm(QuestionType::class, $question);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->flush();
            return $this->redirectToRoute('afficherQuestion');
        }

        return $this->render('/frontEnd/utilisateur/societe/question/manipulerQuestion.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission/questionnaire/question={idQuestion}/supprimer", name="supprimerQuestion")
     */
    public function supprimerQuestion($idQuestion)
    {
        $questionManager = $this->getDoctrine()->getManager();
        $question = $questionManager->getRepository(Question::class)->find($idQuestion);
        $questionManager->remove($question);
        $questionManager->flush();
        return $this->redirectToRoute('afficherQuestion');
    }
}
