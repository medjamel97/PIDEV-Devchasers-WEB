<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterviewRevueController extends AbstractController
{
    /**
     * @Route("/interviewRevue", name="interview_revue")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/interview_revue/afficher.html.twig', [
            'controller_name' => 'InterviewRevueController',
        ]);
    }
}
