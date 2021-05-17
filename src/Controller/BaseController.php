<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("")
     * @Route("publication")
     * @Route("front_end", name="front_end")
     */
    public function accueil(): RedirectResponse
    {
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("espace_societe/publication")
     * @Route("espace_societe", name="espace_societe")
     */
    public function espaceSociete(): RedirectResponse
    {
        return $this->redirectToRoute('accueil_societe');
    }
}
