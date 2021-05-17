<?php

namespace App\Controller\back_end_societe;

use App\Entity\CandidatureMission;
use App\Entity\Mission;
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
class CandidatureMissionController extends AbstractController
{
    /**
     * @Route("candidature_mission")
     */
    public function afficherToutCandidatureMission(Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $request->getSession()->get(Security::LAST_USERNAME)
        ]);

        return $this->render('back_end_societe/societe/mission/candidature_mission/afficher_tout.html.twig', [
            'candidatureMissions' => $this->getDoctrine()->getRepository(CandidatureMission::class)->findBy([
                'mission' => $this->getDoctrine()->getRepository(Mission::class)->findBy([
                    'societe' => $this->getDoctrine()->getRepository(Societe::class)
                        ->find($user->getSociete()->getId())
                ])
            ], [
                'date' => 'DESC'
            ])
        ]);
    }

    /**
     * @Route("candidature_mission/{candidatureMissionId}/afficher")
     */
    public function afficherCandidatureMission($candidatureMissionId): Response
    {
        return $this->render('back_end_societe/societe/mission/candidature_mission/afficher.html.twig', [
            'candidatureMission' => $this->getDoctrine()->getRepository(CandidatureMission::class)
                ->find($candidatureMissionId)
        ]);
    }

    /**
     * @Route("candidature_mission/{candidatureMissionId}/modifier_etat/etat={etat}")
     */
    public function modifierCandidatureMission($candidatureMissionId, $etat): Response
    {
        $manager = $this->getDoctrine()->getManager();

        $candidatureMission = $manager->getRepository(CandidatureMission::class)->find($candidatureMissionId);
        $candidatureMission->setEtat($etat);
        $manager->persist($candidatureMission);
        $manager->flush();

        return $this->redirect('/espace_societe/candidature_mission');
    }
}
