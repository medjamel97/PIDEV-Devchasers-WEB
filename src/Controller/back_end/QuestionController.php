<?php

namespace App\Controller\back_end;

use App\Entity\Mission;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("ajouter_questions")
     */
    public function ajouterQuestions(Request $request)
    {
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($request->get('idMission'));
        $mission->addQuestion(new Question());
    }
}