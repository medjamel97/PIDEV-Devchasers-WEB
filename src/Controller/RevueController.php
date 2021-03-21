<?php

namespace App\Controller;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Revue;
use App\Entity\Societe;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RevueController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/revue/activePage={activePage}", name="afficherToutRevue")
     */
    public function afficherToutRevue($idSociete, $activePage): Response
    {
        $revueRepository = $this->getDoctrine()->getRepository(Revue::class);
        $countItems = $revueRepository->countItemNumber();

        if ($countItems > 0) {
            $itemPerPage = 6;
            $nbPages = intdiv($countItems, $itemPerPage);
            if (($countItems % 6) != 0) $nbPages++;
            $firstItem = ($activePage - 1) * ($itemPerPage);
            if ($activePage > $nbPages || $activePage < 1) return $this->redirect('/societe=' . $idSociete . '/revue/activePage=1');
        } else {
            if ($activePage != 0) return $this->redirect('/societe=' . $idSociete . '/revue/activePage=0');
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
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
        ]);
    }

    /**
     * @Route("addRevuesDebug/{candidatureOffreId}/", name="addRevuesDebug")                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               /offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/revue/ajouter", name="ajouterMultipleRevue")
     */
    public function ajouterMultipleRevue($candidatureOffreId)
    {
        $manager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < 20; $i++) {
            $revue = new Revue();
            $revue->setNbEtoiles(random_int(1, 5))
                ->setObjet("Objet " . $i)
                ->setDescription("Description " . $i)
                ->setCandidatureOffre($manager->getRepository(CandidatureOffre::class)->find($candidatureOffreId));
            $manager->persist($revue);
            $manager->flush();
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/candidatureOffre={idCandidatureOffre}/revue/ajouter", name="ajouterRevue")
     */
    public function ajouterRevue(Request $request, $idSociete, $idOffreDeTravail, $idCandidatureOffre)
    {
        $revue = new Revue();

        $form = $this->createForm(RevueType::class, $revue)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $revue = $form->getData();
            $candidatureOffre = $manager->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
            $revue->setCandidatureOffre($candidatureOffre);
            $manager->persist($revue);
            $manager->flush();

            return $this->redirectToRoute('afficherToutRevue', [
                'idSociete' => $idSociete,
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "Ajouter",
            'form' => $form->createView(),
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'revue' => null,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/revue={idRevue}/modifier", name="modifierRevue")
     */
    public function modifierRevue(Request $request, $idSociete, $idOffreDeTravail, $idRevue)
    {
        $revueRepository = $this->getDoctrine()->getManager();
        $revue = $revueRepository->getRepository(Revue::class)->find($idRevue);

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $revueRepository->flush();
            return $this->redirectToRoute('afficherToutRevue', [
                'idSociete' => $idSociete,
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 1,
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "Modifier",
            'form' => $form->createView(),
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($idSociete),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'revue' => $revue,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/revue={idRevue}/supprimer", name="supprimerRevue")
     */
    public function supprimerRevue($idSociete, $idOffreDeTravail, $idRevue)
    {
        $revueManager = $this->getDoctrine()->getManager();
        $revue = $revueManager->getRepository(Revue::class)->find($idRevue);
        $revueManager->remove($revue);
        $revueManager->flush();
        return $this->redirectToRoute('afficherToutRevue', [
            'idSociete' => $idSociete,
            'idOffreDeTravail' => $idOffreDeTravail,
            'activePage' => 1,
        ]);
    }

    /**
     * @Route("/revue/recherche", name="recherche")
     * @throws ExceptionInterface
     */
    public
    function recherche(Request $request, NormalizerInterface $normalizer)
    {
        $searchValue = $request->get('searchValue');
        if ($searchValue != null) {
            $revues = $this->getDoctrine()->getRepository(Revue::class)->findRevueByNbEtoiles($searchValue);
            $jsonContent = $normalizer->normalize($revues, 'json', ['groups' => 'post:read']);
            $retour = json_encode($jsonContent);
            return new Response($retour);
        } else {
            return new Response(null);
        }
    }
}
