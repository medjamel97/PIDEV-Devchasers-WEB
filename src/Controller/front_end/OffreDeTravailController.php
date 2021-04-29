<?php

namespace App\Controller\front_end;

use App\Entity\Categorie;
use App\Entity\OffreDeTravail;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;


class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("offre_de_travail", name="afficher_tout_offre_de_travail")
     */
    public function afficherToutOffreDeTravail(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('front_end/societe/offre_de_travail/afficher_tout.html.twig', [
            'offresDeTravail' => $paginator->paginate(
                $this->getDoctrine()->getRepository(OffreDeTravail::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("offre_de_travail/categorie={idCategorie}", name="afficher_offre_de_travail_par_categorie")
     */
    public function afficherOffreDeTravailParCategorie($idCategorie, Request $request, PaginatorInterface $paginator)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($idCategorie);
        return $this->render('front_end/offreDeTravail/listoffres.html.twig', [
            'Offre' => $paginator->paginate(
                $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy(['categorie' => $categorie]),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("offre_de_travail ", name="recherche_offre_de_travail")
     * @throws ExceptionInterface
     */
    public function rechercheOffreDeTravail(Request $request, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(OffreDeTravail::class);
        $requestString = $request->get('valeurRecherche');
        $offresDeTravail = $repository->findOffreByNsc($requestString);
        $jsonContent = $Normalizer->normalize($offresDeTravail, 'json', ['groups' => 'get:read']);

        return new Response(json_encode($jsonContent));
    }
}
