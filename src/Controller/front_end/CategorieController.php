<?php

namespace App\Controller\front_end;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    public function afficherToutCategorie(): Response
    {
        return $this->render('front_end/societe/offre_de_travail/categorie/liens.html.twig', [
            'categories' => $this->getDoctrine()->getRepository(Categorie::class)->findAll(),
        ]);
    }
}
