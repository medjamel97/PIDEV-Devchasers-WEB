<?php

namespace App\Controller\back_end;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("back_end/publicationBackEnd", name="afficherToutPublicationBackEnd")
     */
    public function afficherToutPublicationBackEnd()
    {
        return $this->render('/back_end/base.html.twig', [
        ]);
    }

    /**
     * @Route("back_end/publication/{idPublication}/supprimer", name="supprimerPublication")
     */
    public function supprimerPublication($idPublication)
    {
        $publicationManager = $this->getDoctrine()->getManager();
        $publication = $publicationManager->getRepository(Publication::class)->find($idPublication);
        $publicationManager->remove($publication);
        $publicationManager->flush();
        return $this->redirectToRoute('gererPublication');
    }

    /**
     * @Route("back_end/publication/rechreche ", name="rechrechePublication")
     */
    public function searchPub(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("searchValue");
        $titre = $this->getDoctrine()->getRepository(Publication::class)->findStudentByTitre($recherche);
        $jsonContent = $normalizer->normalize($titre, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }
}



    



