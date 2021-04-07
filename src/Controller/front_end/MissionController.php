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
        $missions = $paginator->paginate(
            $this->getDoctrine()->getRepository(Mission::class)->findAll(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('front_end/societe/mission/afficher_tout.html.twig', [
            'missions' => $missions,
        ]);
    }

    /**
     * @Route("mission/recherche", name="recherche_mission")
     * @throws ExceptionInterface
     */
    public function rechercheMission(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("searchValue");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->findOneByMissionName($recherche);

        $jsonContent = $normalizer->normalize($mission, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    /**
     * @Route("mission/addQuest/ajouterchamp/pp", name="ajouterchamp")
     */
    public function ajouterchamp(Request $request): Response
    {
        return new Response(null);

    }

    /**
     * @Route("missionaddQuest/ajoutationQuestionnaire", name="ajoutationQuestionnaire")
     */
    public function ajouterQuestionnaire(Request $request, NormalizerInterface $normalizer): Response
    {
        $id = $request->get("id");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($id);

        $maValeur = $request->get("tab");
        $tab = explode(',', $maValeur);
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($tab); $i++) {
            if ($tab[$i] != "undefined") {
                $Questionnaire = new Questionnaire();
                $Questionnaire->setDescription($tab[$i])->setMission($mission);
                $em->persist($Questionnaire);
                $em->flush();
            }
        }
        return New Response(null);
        //
        // $jsonContent = $normalizer->normalize($tab, 'json',['groups' => 'post:read',]);
        // $retour = json_encode($jsonContent);
        // return new Response($retour);
    }

    /**
     * @Route("mission/candidatureMission/{id}", name="candidatureMission")
     */
    public function candidature(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository(Mission::class)->find($id);
        return $this->render('front_end/societe/mission/candiMission.html.twig', [
            'missions' => $mission,
        ]);
    }

    /**
     * @Route("mission/questMission/{id}", name="questMission")
     */
    public function questionnaire(Request $request, $id): Response
    {
        $reponse = new Questionnaire();
        // $form= $this->createForm(ReponseType::class,$reponse);
        // $form->add('Ajouter',SubmitType::class);
        // $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository(Questionnaire::class)->findBy(['Mission' => $id]);
        $num = $em->getRepository(Question::class)->findAll();
        $i = 0;
        $idreponse = "5";
        foreach ($question as $reponse) {
            $i++;
        }
        foreach ($num as $reponse) {
            $idreponse = $reponse->getId();
        }
        return $this->render('front_end/societe/mission/questMission.html.twig', [
            'question' => $question,
            'nb' => $i,
            'id' => $id,
            'num' => $idreponse
        ]);

    }

    /**
     * @Route("missionquestMission/candidature/i", name="candidature")
     */
    public function Addcandidature(Request $request, NormalizerInterface $normalizer): Response
    {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository(Questionnaire::class)->findBy(['Mission' => $id]);
        $num = $request->get("num");
        $maValeur = $request->get("ch");
        $tab = explode(',', $maValeur);
        // $em=$this->getDoctrine()->getManager();
//replir la table question
        for ($i = 0; $i < count($tab); $i++) {
            if ($tab[$i] != "undefined") {
                $Questionnaire = new Question();
                $Questionnaire->setDescription($tab[$i])->setQuestionnaire($question[$i])->setNumReponse($num);
                $em->persist($Questionnaire);
                $em->flush();
            }
        }
//remplir la table candiature
        $candidat = $em->getRepository(Candidat::class)->find(1);
        $mission = $em->getRepository(Mission::class)->find($id);
        $candidature = new CandidatureMission();
        $candidature->setCandidat($candidat)->setMission($mission)->setNumreponse($num);
        $em->persist($candidature);
        $em->flush();
        return $this->redirectToRoute('mission');
        // $jsonContent = $normalizer->normalize($maValeur, 'json',['groups' => 'post:read',]);
        // $retour = json_encode($jsonContent);
        // return new Response($retour);
    }

    /**
     * @Route("mission/candidatureList/{id}", name="candidatureList")
     */
    public function candidatureList($id)
    {
        $em = $this->getDoctrine()->getManager();
        $candidature = $em->getRepository(CandidatureMission::class)->findBy(['mission' => $id]);
        // $candidat= $em->getRepository(Candidat::class)->find(1);
        $i = 0;
        foreach ($candidature as $reponse) {
            $candidat[$i] = $reponse->getCandidat();
            $i++;
        }
        return $this->render('front_end/societe/mission/candidature.html.twig', [
            'candidature' => $candidature,
            'candidat' => $candidat
        ]);
    }

    /**
     * @Route("mission/reponse/{id}", name="reponse")
     */
    public function reponse($id)
    {
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository(Question::class)->findBy(['num_reponse' => $id]);
        // // $candidat= $em->getRepository(Candidat::class)->find(1);
        $i = 0;
        foreach ($question as $reponse) {
            $quest[$i] = $reponse->getQuestionnaire();
            $i++;
        }
        return $this->render('front_end/societe/mission/reponse.html.twig', [
            'reponse' => $question,
            'question' => $quest
        ]);
    }
}