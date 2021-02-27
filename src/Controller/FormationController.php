<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="formations")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/formation/afficher.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }
}
