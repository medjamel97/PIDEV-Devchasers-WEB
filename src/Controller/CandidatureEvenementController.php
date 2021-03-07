<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureEvenement;
use App\Entity\Evenement;
use App\Form\CandidatureEvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureEvenementController extends AbstractController
{
    /**
     * @Route("", name="")
     */
    public function afficherToutCandidatureEvenement(): Response
    {
        return null;
    }

    /**
     * @Route("/societe={idSociete}/evenement={idEvenement}/candidat={idCandidat}/ajouter", name="ajouterCandidatureEvenement")
     */
    public function ajouterCandidatureEvenement(Request $request,$idSociete,$idEvenement,$idCandidat)
    {
        $candidatureEvenement = new CandidatureEvenement();

        $form = $this->createForm(CandidatureEvenementType::class, $candidatureEvenement);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $candidat = $manager->getRepository(Candidat::class)->find($idCandidat);
            $evenement = $manager ->getRepository(Evenement::class)->find($idEvenement);

            //$candidatureEvenement->setCandidat($candidat);
            $candidatureEvenement->setEvenement($evenement);

            $manager->persist($candidatureEvenement);
            $manager->flush();

            return $this->redirectToRoute("afficherToutEvenement",[
                'idSociete' => $idSociete,
            ]);
        }
        return $this->render('/frontEnd/utilisateur/societe/evenement/ajouterCandidatureOffre.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe={idSociete}/evenement={idEvenement}/candidatureEvenement={idCandidatureEvenement}/supprimer", name="supprimerCandidatureEvenement")
     */
    public function supprimerCandidatureEvenement($idSociete,$idEvenement,$idCandidatureEvenement)
    {
        $candidatureEvenementManager = $this->getDoctrine()->getManager();
        $candidatureEvenement = $candidatureEvenementManager->getRepository(CandidatureEvenement::class)->find($idCandidatureEvenement);
        $candidatureEvenementManager->remove($candidatureEvenement);
        $candidatureEvenementManager->flush();
        return $this->redirectToRoute("afficherToutEvenement",[
            'idSociete' => $idSociete,
        ]);
    }
}
