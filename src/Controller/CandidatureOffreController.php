<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Form\CandidatureOffreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidat={idCandidat}/ajouter", name="ajouterCandidatureOffre")
     */
    public function ajouterCandidatureOffre(Request $request,$idSociete,$idOffreDeTravail,$idCandidat)
    {
        $candidatureOffre = new CandidatureOffre();

        $form = $this->createForm(CandidatureOffreType::class, $candidatureOffre);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $candidat = $manager->getRepository(Candidat::class)->find($idCandidat);
            $offreDeTravail = $manager ->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

            //$candidatureOffre->setCandidat($candidat);
            $candidatureOffre->setOffreDeTravail($offreDeTravail);

            $manager->persist($candidatureOffre);
            $manager->flush();

            return $this->redirectToRoute("afficherToutOffreDeTravail",[
                'idSociete' => $idSociete,
            ]);
        }
        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/ajouterCandidatureOffre.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/supprimer", name="supprimerCandidatureOffre")
     */
    public function supprimerCandidatureOffre($idSociete,$idOffreDeTravail,$idCandidatureOffre)
    {
        $candidatureOffreManager = $this->getDoctrine()->getManager();
        $candidatureOffre = $candidatureOffreManager->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
        $candidatureOffreManager->remove($candidatureOffre);
        $candidatureOffreManager->flush();
        return $this->redirectToRoute("afficherToutOffreDeTravail",[
            'idSociete' => $idSociete,
        ]);
    }
}
