<?php

namespace App\Controller;


use App\Entity\Revue;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RevueController extends AbstractController
{
    /**
     * @Route("/revue", name="revue")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/revue/afficher.html.twig', [
            'controller_name' => 'RevueController',
        ]);
    }
}
