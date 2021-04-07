<?php

namespace App\Controller\front_end;

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
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RevueController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("revue/recherche_societe", name="recherche_societe")
     */
    public function rechercheSociete()
    {
        return $this->render('front_end/societe/offreDeTravail/revue/rechercherSociete.html.twig');
    }

    /**
     * @Route("/revue/offreDeTravail={idOffreDeTravail}/message={message}", name="afficher_tout_revue")
     */
    public function afficherToutRevue($idOffreDeTravail, $message)
    {
        return $this->render('front_end/societe/offreDeTravail/revue/afficher_tout_revue.html.twig', [
            'message' => $message,
            'revues' => $this->getDoctrine()->getRepository(Revue::class)
                ->findBy(['offreDeTravail' => $idOffreDeTravail])
        ]);
    }

    /**
     * @Route("revue/offreDeTravail={idOffreDeTravail}/ajouter", name="ajouter_revue")
     */
    public
    function ajouterRevue(Request $request, $idOffreDeTravail)
    {
        $idCandidat = $this->session->get("utilisateur")["idCandidat"];
        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
        ]);

        if ($idCandidat == null)
            return $this->redirectToRoute('afficher_tout_revue', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'message' => "Vous devez d'abord vous connecter"
            ]);

        if ($candidatureOffre == null)
            return $this->redirectToRoute('afficher_tout_revue', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'message' => "Vous devez d'abord avoir une candidature a cette offre"
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

            return $this->redirectToRoute('afficher_tout_revue', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'message' => "Ajouté avec succes"
            ]);
        }
        return $this->render('front_end/societe/offreDeTravail/revue/manipuler.html.twig', [
            'manipulation' => "Ajouter",
            'form' => $form->createView(),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'revue' => null,
        ]);
    }

    /**
     * @Route("revue/offreDeTravail={idOffreDeTravail}/{idRevue}/modifier", name="modifierRevue")
     */
    public
    function modifierRevue(Request $request, $idOffreDeTravail, $idRevue)
    {
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);
        $candidatureOffre = $revue->getCandidatureOffre();

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $revue->setCandidatureOffre($candidatureOffre);
            $manager->flush();
            return $this->redirectToRoute('afficherRevuesParOffreDeTravail', [
                'idOffreDeTravail' => $idOffreDeTravail,
                'activePage' => 0,
                'message' => "Revue modifié"
            ]);
        }

        return $this->render('front_end/societe/offreDeTravail/revue/manipulerRevue.html.twig', [
            'manipulation' => "Modifier",
            'form' => $form->createView(),
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail),
            'revue' => $revue,
        ]);
    }

    /**
     * @Route("revue/offreDeTravail={idOffreDeTravail}/{idRevue}/supprimer", name="supprimerRevue")
     */
    public
    function supprimerRevue($idRevue, $idOffreDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);
        $manager->remove($revue);
        $manager->flush();
        return $this->redirectToRoute('afficherRevuesParOffreDeTravail', [
            'idOffreDeTravail' => $idOffreDeTravail,
            'activePage' => 0,
            'message' => "Revue supprimé"
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
     * @Route("/revue/ajaxRechercheSociete", name="ajaxRechercheSociete")
     * @throws ExceptionInterface
     */
    public
    function ajaxRechercheSociete(Request $request)
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
