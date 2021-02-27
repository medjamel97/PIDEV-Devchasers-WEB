<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("/offreDeTravail", name="offreDeTravail")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/offreDeTravail/afficher.html.twig', [
            'controller_name' => 'OffreDeTravailController',
        ]);
    }
}
