<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Utilisateur;
use App\Form\CandidatureOffreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureOffreController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/utilisateur={idUtilisateur}/ajouter", name="ajouterCandidatureOffre")
     */
    public function ajouterCandidatureOffre($idSociete, $idOffreDeTravail, $idUtilisateur)
    {
        $manager = $this->getDoctrine()->getManager();
        $utilisateur = $manager->getRepository(Utilisateur::class)->find($idUtilisateur);
        $candidat = $manager->getRepository(Candidat::class)->find($utilisateur->getCandidat()->getID());
        $offreDeTravail = $manager->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

        $candidatureOffre = new CandidatureOffre();
        $candidatureOffre->setCandidat($candidat);
        $candidatureOffre->setOffreDeTravail($offreDeTravail);
      
        $manager->persist($candidatureOffre);
        $manager->flush();
        
        return $this->redirectToRoute("OffreDeTravail", [
            'idSociete' => $idSociete,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/supprimer", name="supprimerCandidatureOffre")
     */
    public function supprimerCandidatureOffre($idSociete, $idOffreDeTravail, $idCandidatureOffre)
    {
        $candidatureOffreManager = $this->getDoctrine()->getManager();
        $candidatureOffre = $candidatureOffreManager->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
        $candidatureOffreManager->remove($candidatureOffre);
        $candidatureOffreManager->flush();
        return $this->redirectToRoute("afficherToutOffreDeTravail", [
            'idSociete' => $idSociete,
            'idCategorie' => "0",
        ]);
    }
}