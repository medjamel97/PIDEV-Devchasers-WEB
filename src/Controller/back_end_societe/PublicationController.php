<?php

namespace App\Controller\back_end_societe;

use App\Entity\Publication;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_societe/")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("accueil_societe", name="accueil_societe")
     */
    public function afficherToutPublicationBackEnd(Request $request): RedirectResponse
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $idSociete = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email])
            ->getSociete()->getId();
        return $this->redirect("societe/" . $idSociete . "/profil");
    }
}



    



