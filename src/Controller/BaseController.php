<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("")
     * @Route("front_end", name="front_end")
     * @Route("accueil", name="accueil")
     */
    public function accueil()
    {
        return $this->redirectToRoute('afficher_tout_publication');
    }

    /**
     * @Route("back_end", name="back_end")
     */
    public function backEnd()
    {
        return $this->redirectToRoute('afficher_tout_publication_back_end');
    }
}