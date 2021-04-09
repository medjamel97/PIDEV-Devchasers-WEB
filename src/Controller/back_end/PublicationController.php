<?php

namespace App\Controller\back_end;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("accueil_societe", name="accueil_societe")
     */
    public function afficherToutPublicationBackEnd()
    {
        return $this->render('back_end/base.html.twig', [
        ]);
    }

    /**
     * @Route("publication/{idPublication}/supprimer")
     */
    public function supprimerPublication($idPublication)
    {
        $publicationManager = $this->getDoctrine()->getManager();
        $publication = $publicationManager->getRepository(Publication::class)->find($idPublication);
        $publicationManager->remove($publication);
        $publicationManager->flush();
        return $this->redirect('/back_end/publication');
    }

    /**
     * @Route("publication/rechreche ")
     */
    public function recherchePublication(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("valeurRecherche");
        $titre = $this->getDoctrine()->getRepository(Publication::class)->findStudentByTitre($recherche);
        $jsonContent = $normalizer->normalize($titre, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }
}



    



