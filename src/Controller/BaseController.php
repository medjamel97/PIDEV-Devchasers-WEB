<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("")
     * @Route("publication")
     * @Route("front_end", name="front_end")
     */
    public function accueil()
    {
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("back_end/publication")
     * @Route("back_end", name="back_end")
     */
    public function backEnd()
    {
        return $this->redirectToRoute('accueil_societe');
    }
}
