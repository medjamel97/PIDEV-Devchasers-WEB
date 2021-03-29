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
     * @Route("/Revue", name="Revue")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/Revue/afficher.html.twig', [
            'controller_name' => 'RevueController',
        ]);
    }
}
