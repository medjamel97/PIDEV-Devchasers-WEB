<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Revue;
use App\Entity\Societe;
use App\Entity\User;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RevueController extends AbstractController
{
    /**
     * @Route("revue/recherche_societe", name="recherche_societe")
     */
    public function rechercheSociete()
    {
        return $this->render('front_end/societe/offre_de_travail/revue/recherche.html.twig');
    }

    /**
     * @Route("revue", name="afficher_tout_revue")
     */
    public function afficherToutRevue()
    {
        return $this->render('front_end/societe/offre_de_travail/revue/afficher_tout.html.twig', [
            'revues' => $this->getDoctrine()->getRepository(Revue::class)->findAll()
        ]);
    }

    /**
     * @Route("revue/{idCandidatureOffre}/ajouter")
     */
    public function ajouterRevue(Request $request, $idCandidatureOffre)
    {
        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($idCandidatureOffre);
        return $this->manipulerRevue($request, 'Ajouter', new Revue(), $candidatureOffre);
    }

    /**
     * @Route("revue/{idRevue}/modifier")
     */
    public function modifierRevue(Request $request, $idRevue)
    {
        $revue = $this->getDoctrine()->getRepository(Revue::class)->find($idRevue);
        $candidatureOffre = $revue->getCandidatureOffre();
        return $this->manipulerRevue($request, 'Modifier', $revue, $candidatureOffre);
    }

    public function manipulerRevue(Request $request, $manipulation, $revue, $candidatureOffre)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(RevueType::class, $revue)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $revue = $form->getData();
                if ($candidatureOffre) $revue->setCandidatureOffre($candidatureOffre);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($revue);
                $entityManager->flush();

                return $this->redirect('/revue');
            }

            return $this->render('front_end/societe/offre_de_travail/revue/manipuler.html.twig', [
                'candidatureOffre' => $candidatureOffre,
                'revue' => $revue,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("revue/{idRevue}/supprimer", name="supprimerRevue")
     */
    public function supprimerRevue($idRevue)
    {
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);
        $manager->remove($revue);
        $manager->flush();
        return $this->redirect('/revue');
    }

    /**
     * @Route("revue/recherche", name="recherche")
     * @throws ExceptionInterface
     */
    public function recherche(Request $request, NormalizerInterface $normalizer)
    {
        $valeurRecherche = $request->get('valeurRecherche');
        if ($valeurRecherche != null) {
            $revues = $this->getDoctrine()->getRepository(Revue::class)->findRevueByNbEtoiles($valeurRecherche);
            $jsonContent = $normalizer->normalize($revues, 'json', ['groups' => 'post:read']);
            $retour = json_encode($jsonContent);
            return new Response($retour);
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("revue/ajaxRechercheSociete", name="ajaxRechercheSociete")
     */
    public function ajaxRechercheSociete(Request $request)
    {
        $valeurRecherche = $request->get('valeurRecherche');
        if ($valeurRecherche != null) {
            $societes = $this->getDoctrine()->getRepository(Societe::class)->findStartingWith($valeurRecherche);

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
