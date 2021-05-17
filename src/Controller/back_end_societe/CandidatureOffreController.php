<?php

namespace App\Controller\back_end_societe;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_societe/")
 */
class CandidatureOffreController extends AbstractController
{
    /**
     * @Route("candidature_offre")
     */
    public function afficherToutCandidatureOffre(Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $request->getSession()->get(Security::LAST_USERNAME)
        ]);

        return $this->render('back_end_societe/societe/offre_de_travail/candidature_offre/afficher_tout.html.twig', [
            'candidatureOffres' => $this->getDoctrine()->getRepository(CandidatureOffre::class)->findBy([
                'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy([
                    'societe' => $this->getDoctrine()->getRepository(Societe::class)
                        ->find($user->getSociete()->getId())
                ])
            ],[
                'date' => 'DESC'
            ])
        ]);
    }

    /**
     * @Route("candidature_offre/{candidatureOffreId}/afficher")
     */
    public function afficherCandidatureOffre($candidatureOffreId): Response
    {
        return $this->render('back_end_societe/societe/offre_de_travail/candidature_offre/afficher.html.twig', [
            'candidatureOffre' => $this->getDoctrine()->getRepository(CandidatureOffre::class)
                ->find($candidatureOffreId)
        ]);
    }

    /**
     * @Route("candidature_offre/{candidatureOffreId}/modifier_etat/etat={etat}")
     */
    public function modifierCandidatureOffre($candidatureOffreId, $etat): Response
    {
        $manager = $this->getDoctrine()->getManager();

        $candidatureOffre = $manager->getRepository(CandidatureOffre::class)->find($candidatureOffreId);
        $candidatureOffre->setEtat($etat);
        $manager->persist($candidatureOffre);
        $manager->flush();

        return $this->redirect('/espace_societe/candidature_offre');
    }
}
