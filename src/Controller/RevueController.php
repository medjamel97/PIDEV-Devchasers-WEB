<?php

namespace App\Controller;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Revue;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RevueController extends AbstractController
{
    /**
     * @Route("/societe={societeID}/revue/activePage={activePage}", name="afficherToutRevue")
     */
    public function afficherToutRevue($societeID, $activePage): Response
    {
        $revueRepository = $this->getDoctrine()->getRepository(Revue::class);
        $countItems = $revueRepository->countItemNumber();

        if ($countItems > 0) {
            $itemPerPage = 6;
            $nbPages = intdiv($countItems, $itemPerPage);
            if (($countItems % 6) != 0) $nbPages++;
            $firstItem = ($activePage - 1) * ($itemPerPage);
            if ($activePage > $nbPages || $activePage < 1 ) return $this->redirect('/societe='.$societeID.'/revue/activePage=1');
        } else {
            if ($activePage != 0) return $this->redirect('/societe='.$societeID.'/revue/activePage=0');
            $firstItem = 0;
            $itemPerPage = 0;
            $nbPages = 0;
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/afficherToutRevue.html.twig', [
            'revues' => $revueRepository->findSinglePageResults($firstItem, $itemPerPage),
            'nbResults' => $countItems,
            'nbPages' => $nbPages,
            'itemPerPage' => $itemPerPage,
            'activePage' => $activePage,
            'firstItem' => $firstItem,
            'societe' => $societeID,
        ]);
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreDeTravailID}/revue/activePage={activePage}", name="afficherRevue")
     */
    public function afficherRevue($societeID, $offreDeTravailID, $activePage): Response
    {
        return null;
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreDeTravailID}/candidatureOffre={candidatureOffreID}/revue/ajouter", name="ajouterRevue")
     */
    public function ajouterRevue(Request $request, $societeID, $offreDeTravailID, $candidatureOffreID)
    {
        $revue = new Revue();

        $form = $this->createForm(RevueType::class, $revue)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $revue = $form->getData();
            $candidatureOffre = $manager->getRepository(CandidatureOffre::class)->find($candidatureOffreID);
            $revue->setCandidatureOffre($candidatureOffre);
            $manager->persist($revue);
            $manager->flush();

            return $this->redirectToRoute('afficherToutRevue', [
                'societeID' => $societeID,
                'offreDeTravailID' => $offreDeTravailID,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "ajouter",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreDeTravailID}/revue{revueID}/modifier", name="modifierRevue")
     */
    public function modifierRevue(Request $request, $societeID, $offreDeTravailID, $revueID)
    {
        $revueRepository = $this->getDoctrine()->getManager();
        $revue = $revueRepository->getRepository(Revue::class)->find($revueID);

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $revueRepository->flush();
            return $this->redirectToRoute('afficherToutRevue', [
                'societeID' => $societeID,
                'offreDeTravailID' => $offreDeTravailID,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "modifier",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/societe={societeID}/offreDeTravail={offreDeTravailID}/revue={revueID}/supprimer", name="supprimerRevue")
     */
    public function supprimerRevue($societeID, $offreDeTravailID, $revueID)
    {
        $revueManager = $this->getDoctrine()->getManager();
        $revue = $revueManager->getRepository(Revue::class)->find($revueID);
        $revueManager->remove($revue);
        $revueManager->flush();
        return $this->redirectToRoute('afficherToutRevue', [
            'societeID' => $societeID,
            'offreDeTravailID' => $offreDeTravailID,
            'activePage' => 1,
        ]);
    }
}
