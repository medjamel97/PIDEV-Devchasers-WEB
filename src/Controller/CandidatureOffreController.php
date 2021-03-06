<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureOffreController extends AbstractController
{
    /**
     * @Route("", name="")
     */
    public function afficherToutCandidatureOffre(): Response
    {
        return null;
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreID}/candidatureOffre/ajouter", name="ajouterCandidatureOffre")
     */
    public function ajouterCandidatureOffre($societeID,$offreID)
    {
        $manager = $this->getDoctrine()->getManager();

        $candidatureOffre = new CandidatureOffre();
        $candidat = $manager->getRepository(Candidat::class)->find(1);
        $offreDeTravail = $manager ->getRepository(OffreDeTravail::class)->find($offreID);
        $candidatureOffre->setCandidat($candidat)->setOffreDeTravail($offreDeTravail);

        $manager->persist($candidatureOffre);
        $manager->flush();

        return $this->redirectToRoute("afficherToutOffreDeTravail",[
            'societeID' => $societeID,
            'categorieID' => 0,
        ]);
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreID}/candidatureOffre={candidatureOffreID}/supprimer", name="supprimerCandidatureOffre")
     */
    public function supprimerCandidatureOffre($societeID,$candidatureOffreID)
    {
        $candidatureOffreManager = $this->getDoctrine()->getManager();
        $candidatureOffre = $candidatureOffreManager->getRepository(CandidatureOffre::class)->find($candidatureOffreID);
        $candidatureOffreManager->remove($candidatureOffre);
        $candidatureOffreManager->flush();
        return $this->redirectToRoute("afficherToutOffreDeTravail",[
            'societeID' => $societeID,
            'categorieID' => 0,
        ]);
    }
}
