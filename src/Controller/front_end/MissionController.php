<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\CandidatureMission;
use App\Entity\Mission;
use App\Entity\Question;
use App\Entity\Questionnaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class MissionController extends AbstractController
{
    /**
     * @Route("mission", name="afficher_tout_mission")
     */
    public function afficherToutMission(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('front_end/societe/mission/afficher_tout.html.twig', [
            'missions' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Mission::class)->findAll(),
                $request->query->getInt('page', 1), 10
            ),
        ]);
    }

    /**
     * @Route("mission/recherche", name="recherche_mission")
     * @throws ExceptionInterface
     */
    public function rechercheMission(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("valeurRecherche");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->findOneByMissionName($recherche);

        $jsonContent = $normalizer->normalize($mission, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

}