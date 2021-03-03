<?php

namespace App\Controller;

use App\Entity\OffreDeTravail;
use App\Form\OffreDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("/offreDeTravail", name="afficherOffreDeTravail")
     */
    public function afficherOffreDeTravail(): Response
    {
        return $this->render('/frontEnd/societe/offreDeTravail/afficher.html.twig', [

        ]);
    }
}
