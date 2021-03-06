<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/accueil.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }

    /**
     * @Route("/acceuilBackEnd", name="acceuilBackEnd")
     */
    public function indexBackEnd(): Response
    {
        return $this->render('/backEnd/accueil.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
}
