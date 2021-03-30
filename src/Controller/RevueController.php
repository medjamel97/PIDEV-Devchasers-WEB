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
     * @Route("/societe={societe}/offreDeTravail={offreDeTravail}/revue/pageIndex={pageIndex}", name="afficherRevue")
     */
    public function afficherRevue($societe,$offreDeTravail,$pageIndex): Response
    {
        $revueRepository = $this->getDoctrine()->getManager()->getRepository(Revue::class);

        $CandidatureOffreRepository = $this->getDoctrine()->getManager()->getRepository(CandidatureOffre::class);

        $itemPerPage = 6;

        $firstItem = ($pageIndex-1)*($itemPerPage);

        $revues = $revueRepository->findSinglePageResults($firstItem, $itemPerPage);

        $debug = "";

        $candidatureOffres = $CandidatureOffreRepository->getCandidatureOffreId();

        foreach ($candidatureOffres as $candidatureOffre){
            foreach ($revues as $revue){
                $NouvRevue = new Revue();
                $NouvRevue = $candidatureOffre;
                if ($NouvRevue->getId() == $revue->getId()){
                    $debug = "Triggered";
                }
            }
        }

        $countItems = $revueRepository->countItemNumber();

        $nbPages = intdiv($countItems, $itemPerPage);

        if (($countItems % 6) != 0 ){
            $nbPages++;
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/afficherCompetence.html.twig', [
            'Debug' => $debug,
            'Revue' => $revues,
            'CandidatureOffre' => $candidatureOffres,
            'itemPerPage' => $itemPerPage,
            'nbResults' => $countItems,
            'nbPages' => $nbPages,
            'activePage' => $pageIndex,
            'firstItem' => $firstItem,
        ]);
    }

    /**
     * @Route("/revue/ajouterDebug/page={pageIndex}", name="ajouterRevueDebug")
     */
    public function ajouterRevueDebug(Request $request,$pageIndex)
    {
        $revueRepository = $this->getDoctrine()->getManager();
        for ($i = 1; $i < 51; $i++) {
            $revue = new Revue();
            $revue->setNbEtoiles(random_int(0, 5))
                ->setObjet("Revue " . $i)
                ->setDescription("Description " . $i);
            $revueRepository->persist($revue);
            $revueRepository->flush();
        }
        return $this->redirectToRoute('afficherRevue', ['societe' => 0,'offreDeTravail' => 0,'pageIndex' => $pageIndex]);
    }


    /**
     * @Route("/revue/ajouter/page={pageIndex}", name="ajouterRevue")
     */
    public function ajouterRevue(Request $request,$pageIndex)
    {
        $revue = new Revue();

        $form = $this->createForm(RevueType::class, $revue)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $revue = $form->getData();

            $revueRepository = $this->getDoctrine()->getManager();
            $revueRepository->persist($revue);
            $revueRepository->flush();

            return $this->redirectToRoute('afficherRevue', ['societe' => 0,'offreDeTravail' => 0,'pageIndex' => $pageIndex]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerCompetence.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/revue/modifier/page={pageIndex}/id={id}", name="modifierRevue")
     */
    public function modifierRevue(Request $request,$pageIndex , $id)
    {
        $revueRepository = $this->getDoctrine()->getManager();
        $revue = $revueRepository->getRepository(Revue::class)->find($id);

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $revueRepository->flush();
            return $this->redirectToRoute('afficherRevue', ['societe' => 0,'offreDeTravail' => 0,'pageIndex' => $pageIndex]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerQuestionnaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/revue/supprimer/page={pageIndex}/id={id}", name="supprimerRevue")
     */
    public function supprimerRevue($id, $pageIndex)
    {
        $revueManager = $this->getDoctrine()->getManager();
        $revue = $revueManager->getRepository(Revue::class)->find($id);
        $revueManager->remove($revue);
        $revueManager->flush();
        return $this->redirectToRoute('afficherRevue', ['societe' => 0,'offreDeTravail' => 0,'pageIndex' => $pageIndex]);
    }

    /**
     * @Route("/revue/supprimerDebug/page={pageIndex}/id={id}", name="supprimerRevueDebug")
     */
    public function supprimerRevueDebug($id, $pageIndex)
    {
        $revueManager = $this->getDoctrine()->getManager();
        $length = $revueManager->getRepository(Revue::class)->countItemNumber();
        while ($length > 0) {
            $length --;
            $revue = $revueManager->getRepository(Revue::class)->findFirst();
            $revueManager->remove($revue);
            $revueManager->flush();
        }
        return $this->redirectToRoute('afficherRevue', ['societe' => 0,'offreDeTravail' => 0,'pageIndex' => $pageIndex]);
    }
}
