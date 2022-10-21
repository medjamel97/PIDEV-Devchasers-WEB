<?php

namespace App\Controller\back_end_societe;

use App\Entity\CandidatureMission;
use App\Entity\CandidatureOffre;
use App\Entity\Mission;
use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_societe/")
 */
class NotificationController extends AbstractController
{
    public function afficherToutNotification(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $offresDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy([
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($user->getSociete()->getId())
        ]);
        $missions = $this->getDoctrine()->getRepository(Mission::class)->findBy([
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($user->getSociete()->getId())
        ]);

        $candidatureOffres = $this->getDoctrine()->getRepository(CandidatureOffre::class)
            ->findBy([
                'offreDeTravail' => $offresDeTravail,
                'etat' => 'non traité'
            ]);
        $candidatureMissions = $this->getDoctrine()->getRepository(CandidatureMission::class)
            ->findBy([
                'mission' => $missions,
                'etat' => 'non traité'
            ]);

        $notifications = [];
        $i = 0;
        foreach ($candidatureOffres as $candidatureOffre) {
            $notifications[$i]['date'] = $candidatureOffre->getDate()->format('H:i - d/M/Y');
            $notifications[$i]['idCandidature'] = $candidatureOffre->getId();
            $notifications[$i]['typeCandidature'] = 'offre';
            $notifications[$i]['etat'] = $candidatureOffre->getEtat();
            $notifications[$i]['candidat'] =
                $candidatureOffre->getCandidat()->getPrenom() . ' ' .
                $candidatureOffre->getCandidat()->getNom();
            $notifications[$i]['offre'] = $candidatureOffre->getOffreDeTravail()->getNom();
            $i++;
        }
        foreach ($candidatureMissions as $candidatureMission) {
            $notifications[$i]['date'] = $candidatureMission->getDate()->format('H:i - d/M/Y');
            $notifications[$i]['idCandidature'] = $candidatureMission->getId();
            $notifications[$i]['typeCandidature'] = 'mission';
            $notifications[$i]['etat'] = $candidatureMission->getEtat();
            $notifications[$i]['candidat'] =
                $candidatureMission->getCandidat()->getPrenom() . ' ' .
                $candidatureMission->getCandidat()->getNom();
            $notifications[$i]['offre'] = $candidatureMission->getMission()->getNom();
            $i++;
        }

        asort($notifications);

        return $this->render('back_end_societe/societe/notification/afficher_tout_notification.html.twig', [
            'notifications' => $notifications
        ]);
    }
}
