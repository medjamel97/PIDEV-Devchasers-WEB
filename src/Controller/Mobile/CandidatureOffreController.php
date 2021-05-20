<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
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
            $jsonContent[0]['id'] = $candidatureOffre->getId();
            $jsonContent[0]['offreDeTravailId'] = $candidatureOffre->getOffreDeTravail()->getId();
            $jsonContent[0]['candidatId'] = $candidatureOffre->getCandidat()->getId();
            $jsonContent[0]['etat'] = $candidatureOffre->getEtat();
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }
}
