<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionController extends AbstractController
{
    /**
     * @Route("/mission", name="mission")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/mission/afficher.html.twig', [
            'controller_name' => 'MissionController',
        ]);
    }
}
