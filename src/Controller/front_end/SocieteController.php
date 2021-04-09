<?php

namespace App\Controller\front_end;

use App\Entity\Societe;
use App\Entity\User;
use App\Form\SocieteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    /**
     * @Route("societe", name="afficher_tout_societe")
     */
    public function afficherToutSociete()
    {
        return $this->render('front_end/societe/afficher_tout.html.twig', [
            'societes' => $this->getDoctrine()->getRepository(Societe::class)->findAll(),
        ]);
    }

    /**
     * @Route("societe/{idSociete}/afficher", name="afficher_societe")
     */
    public function afficherSociete($idSociete)
    {
        return $this->render('front_end/societe/afficher_tout.html.twig', [
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
        ]);
    }
}