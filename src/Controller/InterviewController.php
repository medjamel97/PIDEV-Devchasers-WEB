<?php

namespace App\Controller;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Interview;
use App\Entity\Societe;
use App\Form\InterviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterviewController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/interview/activePage={activePage}", name="afficherToutInterview")
     */
    public function afficherToutInterview($idSociete, $activePage): Response
    {
        $interviewRepository = $this->getDoctrine()->getRepository(Interview::class);
        $countItems = $interviewRepository->countItemNumber();

        if ($countItems > 0) {
            $itemPerPage = 6;
            $nbPages = intdiv($countItems, $itemPerPage);
            if (($countItems % 6) != 0) $nbPages++;
            $firstItem = ($activePage - 1) * ($itemPerPage);
            if ($activePage > $nbPages || $activePage < 1 ) return $this->redirect('/societe='.$idSociete.'/interview/activePage=1');
        } else {
            if ($activePage != 0) return $this->redirect('/societe='.$idSociete.'/interview/activePage=0');
            $firstItem = 0;
            $itemPerPage = 0;
            $nbPages = 0;
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/interview/afficherToutInterview.html.twig', [
            'interviews' => $interviewRepository->findSinglePageResults($firstItem, $itemPerPage),
            'nbResults' => $countItems,
            'nbPages' => $nbPages,
            'itemPerPage' => $itemPerPage,
            'activePage' => $activePage,
            'firstItem' => $firstItem,
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
        ]);
    }

    /**
     * @Route("addInterviewsDebug/{candidatureOffreId}/", name="addInterviewsDebug")                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               /offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/interview/ajouter", name="ajouterMultipleInterview")
     */
    public function ajouterMultipleInterview($candidatureOffreId)
    {
        $manager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < 20; $i++) {
            $interview = new Interview();
            $interview->setNbEtoiles(random_int(1, 5))
                ->setObjet("Objet " . $i)
                ->setDescription("Description " . $i)
                ->setCandidatureOffre($manager->getRepository(CandidatureOffre::class)->find($candidatureOffreId));
            $manager->persist($interview);
            $manager->flush();
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/interview/ajouter", name="ajouterInterview")
     */
    public function ajouterInterview(Request $request, $idSociete, $idOffreDeTravail, $idCandidatureOffre)
    {
        $interview = new Interview();

        $form = $this->createForm(InterviewType::class, $interview)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $interview = $form->getData();
            $candidatureOffre = $manager->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
            $interview->setCandidatureOffre($candidatureOffre);
            $manager->persist($interview);
            $manager->flush();

            return $this->redirectToRoute('afficherToutInterview', [
                'idSociete' => $idSociete,
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/interview/manipulerInterview.html.twig', [
            'manipulation' => "Ajouter",
            'form' => $form->createView(),
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'interview' => null,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/interview={idInterview}/modifier", name="modifierInterview")
     */
    public function modifierInterview(Request $request, $idSociete, $idOffreDeTravail, $idInterview)
    {
        $interviewRepository = $this->getDoctrine()->getManager();
        $interview = $interviewRepository->getRepository(Interview::class)->find($idInterview);

        $form = $this->createForm(InterviewType::class, $interview);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $interviewRepository->flush();
            return $this->redirectToRoute('afficherToutInterview', [
                'idSociete' => $idSociete,
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/interview/manipulerInterview.html.twig', [
            'manipulation' => "Modifier",
            'form' => $form->createView(),
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'interview' => $interview,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/interview={idInterview}/supprimer", name="supprimerInterview")
     */
    public function supprimerInterview($idSociete, $idOffreDeTravail, $idInterview)
    {
        $interviewManager = $this->getDoctrine()->getManager();
        $interview = $interviewManager->getRepository(Interview::class)->find($idInterview);
        $interviewManager->remove($interview);
        $interviewManager->flush();
        return $this->redirectToRoute('afficherToutInterview', [
            'idSociete' => $idSociete,
            'idOffreDeTravail' => $idOffreDeTravail,
            'activePage' => 1,
        ]);
    }
}