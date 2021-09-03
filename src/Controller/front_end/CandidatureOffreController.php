<?php

namespace App\Controller\front_end;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CandidatureOffreController extends AbstractController
{
    /**
     * @Route("candidature_offre/{idOffreDeTravail}/ajouter")
     */
    public function ajouterCandidatureOffre(Request $request, $idOffreDeTravail): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        $offreDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

        $checkCandidature = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
            'candidat' => $user->getCandidat(),
            'offreDeTravail' => $offreDeTravail,
        ]);

        if (!$checkCandidature) {
            $candidatureOffre = new CandidatureOffre();
            try {
                $candidatureOffre
                    ->setCandidat($user->getCandidat())
                    ->setOffreDeTravail($offreDeTravail)
                    ->setDate(new DateTime('now', new DateTimeZone('Africa/Tunis')));
            } catch (\Exception $e) {
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidatureOffre);
            $entityManager->flush();
        }

        return $this->redirect('/offre_de_travail/' . $idOffreDeTravail . '/afficher');
    }
}
