<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Revue;
use App\Entity\Societe;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RevueController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/revue/societe={idSociete}/activePage={activePage}", name="afficherRevuesParSociete")
     */
    public function afficherRevuesParSociete($idSociete, $activePage): Response
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
            if ($activePage != 0) return $this->redirect('/revue/societe=' . $idSociete . '/activePage=0');
            $firstItem = 0;
            $itemPerPage = 0;
            $nbPages = 0;
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/afficherRevuesParSociete.html.twig', [
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
     * @Route("/revue/offreDeTravail={idOffreDeTravail}/activePage={activePage}/message={message}", name="afficherRevuesParOffreDeTravail")
     */
    public function afficherRevuesParOffreDeTravail($idOffreDeTravail, $activePage, $message): Response
    {
        $revueRepository = $this->getDoctrine()->getRepository(Revue::class);
        $countItems = $revueRepository->countItemNumber();

        if ($countItems > 0) {
            $itemPerPage = 6;
            $nbPages = intdiv($countItems, $itemPerPage);
            if (($countItems % 6) != 0) $nbPages++;
            $firstItem = ($activePage - 1) * ($itemPerPage);
            if ($activePage > $nbPages || $activePage < 1) return $this->redirect('/offreDeTravail=' . $idOffreDeTravail . '/revue/activePage=1');
        } else {
            if ($activePage != 0) return $this->redirect('/revue/offreDeTravail=' . $idOffreDeTravail . '/activePage=0');
            $firstItem = 0;
            $itemPerPage = 0;
            $nbPages = 0;
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/afficherRevuesParOffre.html.twig', [
            'message' => $message,
            'revues' => $revueRepository->findSinglePageResults($firstItem, $itemPerPage),
            'nbResults' => $countItems,
            'nbPages' => $nbPages,
            'itemPerPage' => $itemPerPage,
            'activePage' => $activePage,
            'firstItem' => $firstItem,
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
        ]);
    }

    /**
     * @Route("revue/offreDeTravail={idOffreDeTravail}/ajouter", name="ajouterRevue")
     */
    public function ajouterRevue(Request $request, $idOffreDeTravail)
    {
        $idCandidat = $this->session->get("utilisateur")["idCandidat"];

        if ($idCandidat == null)
            return $this->redirectToRoute('afficherRevuesParOffreDeTravail', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 0,
                'message' => "Vous devez d'abord vous connecter"
            ]);


        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
        ]);


        if ($candidatureOffre == null)
            return $this->redirectToRoute('afficherRevuesParOffreDeTravail', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 0,
                'message' => "Vous devez d'abord candidater a cette offre"
            ]);


        $revue = new Revue();

        $form = $this->createForm(RevueType::class, $revue)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $revue = $form->getData();
            $revue->setCandidatureOffre($candidatureOffre);
            $manager->persist($revue);
            $manager->flush();

            return $this->redirectToRoute('afficherRevuesParOffreDeTravail', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 0,
                'message' => "Ajouté avec succes"
            ]);
        }
        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "Ajouter",
            'form' => $form->createView(),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'revue' => null,
        ]);
    }

    /**
     * @Route("/societe={idSociete}/offreDeTravail={idOffreDeTravail}/revue={idRevue}/modifier", name="modifierRevue")
     */
    public function modifierRevue(Request $request, $idSociete, $idOffreDeTravail, $idRevue)
    {
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
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
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);
        $manager->remove($revue);
        $manager->flush();
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
    public function recherche(Request $request, NormalizerInterface $normalizer)
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

    /**
     * @Route("revue/rechercheSociete", name="rechercheSociete")
     * @throws ExceptionInterface
     */
    public function rechercheSociete(Request $request)
    {
        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/revue/rechercherSociete.html.twig', [
        ]);
    }

    /**
     * @Route("/revue/ajaxRechercheSociete", name="ajaxRechercheSociete")
     * @throws ExceptionInterface
     */
    public function ajaxRechercheSociete(Request $request)
    {
        $searchValue = $request->get('searchValue');
        if ($searchValue != null) {
            $societes = $this->getDoctrine()->getRepository(Societe::class)->findStartingWith($searchValue);

            $jsonContent = null;
            $i = 0;
            foreach ($societes as $societe) {
                $jsonContent[$i]['id'] = $societe->getId();
                $jsonContent[$i]['nom'] = $societe->getNomSociete();
                $jsonContent[$i]['idPhoto'] = $societe->getIdPhotoSociete();
                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }
}
