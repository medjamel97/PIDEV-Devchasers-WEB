<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;

class BaseController extends AbstractController
{
    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/acceuil.html.twig', [
            'controller_name' => 'BaseController',
            'categories' => $this->getDoctine()->getRepository(Categorie::class)->findAll()
        ]);
    }

    /**
     * @Route("/accueilBackEnd", name="accueilBackEnd")
     */
    public function indexBackEnd(): Response
    {
        return $this->render('/backEnd/acceuil.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
}
