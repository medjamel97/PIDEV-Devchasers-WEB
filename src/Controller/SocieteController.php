<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    /**
     * @Route("/societe", name="societe")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/societe/afficher.html.twig', [
            'controller_name' => 'SocieteController',
        ]);
    }
}
