<?php

namespace App\Controller\front_end;

use App\Entity\CandidatureMission;
use App\Entity\Mission;
use App\Entity\Question;
use App\Entity\User;
use App\Form\QuestionType;
use DateTime;
use DateTimeZone;
use Exception;
use phpDocumentor\Reflection\Types\ClassString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class MissionController extends AbstractController
{
    /**
     * @Route("mission", name="afficher_tout_mission")
     */
    public function afficherToutMission(Request $request, PaginatorInterface $paginator): Response
    {
        $missions = $this->getDoctrine()->getRepository(Mission::class)->findAll();

        return $this->render('front_end/societe/mission/afficher_tout.html.twig', [
            'totalMissions' => count($missions),
            'missions' => $paginator->paginate(
                $missions,
                $request->query->getInt('page', 1), 6
            ),
        ]);
    }

    /**
     * @Route("mission/{missionId}/afficher", name="afficher_mission")
     */
    public function afficherMission($missionId): Response
    {
        return $this->render('front_end/societe/mission/afficher.html.twig', [
            'mission' => $this->getDoctrine()->getRepository(Mission::class)->find($missionId),
        ]);
    }

    /**
     * @Route("mission/{missionId}/postuler", name="postuler_mission")
     */
    public function postulerMission($missionId): Response
    {
        return $this->render('front_end/societe/mission/postuler.html.twig', [
            'mission' => $this->getDoctrine()->getRepository(Mission::class)->find($missionId),
        ]);
    }

    /**
     * @Route("mission/{missionId}/candidater_mission", name="candidater_mission")
     * @throws Exception
     */
    public function candidaterMission(Request $request, $missionId): Response
    {
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($missionId);

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $i = 1;
        $isCorrect = true;
        foreach ($mission->getQuestion() as $question) {
            if (strtolower($question->getReponse()) != strtolower($request->get('reponse-' . $i))) {
                $isCorrect = false;
            }
            $i++;
        }
        if ($isCorrect) {
            $candidatureMission = $this->getDoctrine()->getRepository(CandidatureMission::class)
                ->findOneBy([
                    'candidat' => $user->getCandidat(),
                    'mission' => $mission,
                ]);
            if ($candidatureMission == null) {
                $candidatureMission = new CandidatureMission();
                $candidatureMission
                    ->setCandidat($user->getCandidat())
                    ->setMission($mission)
                    ->setDate(new DateTime('now', new DateTimeZone('Africa/Tunis')));

                $this->getDoctrine()->getManager()->persist($candidatureMission);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash("success", "Votre candidature a été bien prise en compte");
            } else {
                $this->addFlash("error", "Vous avez deja candidaté a cette mission");
            }
        } else {
            $this->addFlash("error", "Vous n'avez pas bien repondu au quiz");
        }

        return $this->render('front_end/societe/mission/afficher.html.twig', [
            'mission' => $this->getDoctrine()->getRepository(Mission::class)->find($missionId),
        ]);
    }

    /**
     * @Route("mission/recherche", name="recherche_mission")
     * @throws ExceptionInterface
     */
    public function rechercheMission(Request $request, NormalizerInterface $normalizer): Response
    {
        $recherche = $request->get("valeurRecherche");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->findOneByMissionName($recherche);

        $jsonContent = $normalizer->normalize($mission, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }


    /**
     * @Route("map/{lat}/{long}", name="map")
     */
    public function index($lat, $long): Response
    {
        return $this->render('front_end/societe/mission/map.html.twig', [
            'lat' => $lat,
            'long' => $long
        ]);
    }

    //   /**
    //  * @Route("map", name="map")
    //  */
    // public function afficherToutMission(Request $request)
    // {
    //     return $this->render('front_end/societe/mission/map.html.twig', [  ]);
    // }

}
