<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class CandidatureOffreController extends AbstractController
{
    /**
     * @Route("recuperer_candidature_offre_par_offre_candidat")
     * @param Request $request
     * @return Response
     */
    public function recupererCandidatureOffreParId(Request $request)
    {
        $offreDeTravailId = (int)$request->get("offreDeTravailId");
        $candidatId = (int)$request->get("candidatId");

        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
            "offreDeTravail" => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($offreDeTravailId),
            "candidat" => $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId),
        ]);
        if ($candidatureOffre != null) {
            $jsonContent['id'] = $candidatureOffre->getId();
            $jsonContent['offreDeTravailId'] = $candidatureOffre->getOffreDeTravail()->getId();
            $jsonContent['candidatId'] = $candidatureOffre->getCandidat()->getId();
            $jsonContent['etat'] = $candidatureOffre->getEtat();
            return new Response(json_encode($jsonContent));
        } else {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
    }

    /**
     * @Route("ajouter_Candidature")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function ajouterCandidature(Request $request)
    {
        $idCandidat = (int)$request->get("candidatId");
        $idOffre = (int)$request->get("offreDeTravailId");
        $etat = $request->get("etat");

        $offreDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffre);
        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat);

        $can = (
        $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
            "candidat" => $candidat,
            "offreDeTravail" => $offreDeTravail,
        ])
        );
        if ($can == null) {
            $can = new CandidatureOffre();
            $can
                ->setCandidat($candidat)
                ->setOffreDeTravail($offreDeTravail)
                ->setEtat($etat);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($can);
            $manager->flush();
        } else {
        }

        return new Response("Ajout effectué");
    }

    /**
     * @Route("modifier_CandidatureOffre")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function modifierEtatCandidature(Request $request)
    {
        $candidatureOffreId = (int)$request->get("candidatureOffreId");
        $etat = $request->get("etat");

        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($candidatureOffreId);

        $candidatureOffre->setEtat($etat);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($candidatureOffre);
        $manager->flush();

        return new Response("Modification effectué");
    }

    /**
     * @Route("recuperer_MesCandidatureOffre")
     * @return Response
     */
    public function recupererOffreParSociete(Request $request)
    {
        $societeId = (int)$request->get("societeId");

        $societe = $this->getDoctrine()->getRepository(Societe::class)->find($societeId);

        $offresDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)
            ->findBy(['societe' => $societe]);

        $candidaturesoffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findBy(
            ["offreDeTravail" => $offresDeTravail]
        );

        $jsonContent = null;
        if ($candidaturesoffre) {
            $j = 0;
            foreach ($candidaturesoffre as $candidatureoffre) {
                $jsonContent[$j]['id'] = $candidatureoffre->getId();
                $jsonContent[$j]['nomOffre'] = $candidatureoffre->getOffreDeTravail()->getNom();
                $jsonContent[$j]['nomPrenomCandidat'] =
                    $candidatureoffre->getCandidat()->getPrenom() . ' ' . $candidatureoffre->getCandidat()->getNom();
                $jsonContent[$j]['etat'] = $candidatureoffre->getEtat();
                $j++;
            }
            return new Response(json_encode($jsonContent));
        }
        return new Response(null);
    }
}
