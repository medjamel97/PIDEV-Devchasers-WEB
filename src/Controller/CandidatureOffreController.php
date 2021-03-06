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
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre/ajouter", name="ajouterCandidatureOffre")
     */
    public function ajouterCandidatureOffre($idSociete,$idOffreDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();

        $candidatureOffre = new CandidatureOffre();
        $candidat = $manager->getRepository(Candidat::class)->find(3);
        $offreDeTravail = $manager ->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);
        $candidatureOffre->setCandidat($candidat)->setOffreDeTravail($offreDeTravail);

        $manager->persist($candidatureOffre);
        $manager->flush();

        return $this->redirectToRoute("afficherToutOffreDeTravail",[
            'idSociete' => $idSociete,
            'idCategorie' => 0,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/supprimer", name="supprimerCandidatureOffre")
     */
    public function supprimerCandidatureOffre($idSociete,$idCandidatureOffre)
    {
        $candidatureOffreManager = $this->getDoctrine()->getManager();
        $candidatureOffre = $candidatureOffreManager->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
        $candidatureOffreManager->remove($candidatureOffre);
        $candidatureOffreManager->flush();
        return $this->redirectToRoute("afficherToutOffreDeTravail",[
            'idSociete' => $idSociete,
            'idCategorie' => 0,
        ]);
    }
}
