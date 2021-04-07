<?php

namespace App\Controller\front_end;

use App\Entity\Categorie;
use App\Entity\OffreDeTravail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;


class OffreDeTravailController extends Controller
{
    /**
     * @Route("offre_de_travail", name="afficher_tout_offre_de_travail")
     */
    public function afficherToutOffreDeTravail(Request $request)
    {
        $offreDeTravails = $this->getDoctrine()->getRepository(OffreDeTravail::class)->findAll();

        $paginator = $this->get('knp_paginator');
        $offreDeTravails = $paginator->paginate(
            $offreDeTravails,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );

        return $this->render('front_end/societe/offre_de_travail/afficher_tout.html.twig', [
            'offreDeTravails' => $offreDeTravails,
        ]);
    }

    /**
     * @Route("offre_de_travail/{idOffreDeTravail}/delete", name="supprimer_offre_de_travail")
     */
    public function supprimerOffreDeTravail($idOffreDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();
        $entity = $manager->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);
        $manager->remove($entity);
        $manager->flush();

        return $this->redirectToRoute('afficher_tout_offre_de_travail');
    }

    /**
     * @Route("offre_de_travail/categorie={id}", name="offre_de_travail")
     */
    public function show(int $id, Request $request): Response
    {
        $offre = $this->getDoctrine()
            ->getRepository(OffreDeTravail::class)
            ->findBy(['categorie' => $this->getDoctrine()->getRepository(Categorie::class)->find($id)]);

        $paginator = $this->get('knp_paginator');
        $res = $paginator->paginate(
            $offre,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );

        return $this->render('/frontEnd/offreDeTravail/listoffres.html.twig', [
            'Offre' => $res,
        ]);
    }

    /**
     * @Route("offre_de_travail ", name="searchOffre")
     */
    public function searchOffre(Request $request, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(OffreDeTravail::class);
        $requestString = $request->get('searchValue');
        $offres = $repository->findOffreByNsc($requestString);
        $jsonContent = $Normalizer->normalize($offres, 'json', ['groups' => 'get:read']);

        return new Response(json_encode($jsonContent));
    }
}