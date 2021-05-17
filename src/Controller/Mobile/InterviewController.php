<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\Interview;
use App\Entity\OffreDeTravail;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class InterviewController extends AbstractController
{

    /**
     * @Route("recuperer_interviews")
     * @return Response
     */
    public function recupererInterviews()
    {
        $interviews = $this->getDoctrine()->getRepository(Interview::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $interview = new Interview();
        foreach ($interviews as $interview) {
            $jsonContent[$i]['id'] = $interview->getId();
            $jsonContent[$i]['idCandidatureOffre'] = $interview->getCandidatureOffre()->getId();
            $jsonContent[$i]['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
            $jsonContent[$i]['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
            $jsonContent[$i]['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
            $jsonContent[$i]['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
            $jsonContent[$i]['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
            $jsonContent[$i]['difficulte'] = $interview->getDifficulte();
            $jsonContent[$i]['description'] = $interview->getDescription();
            $jsonContent[$i]['dateCreation'] = $interview->getDateCreation()->format('H:i - d/M/Y');
            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_interview_par_id")
     * @param Request $request
     * @return Response
     */
    public function recupererInterviewParId(Request $request)
    {
        $idInterview = (int)$request->get("id");

        $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);

        $jsonContent['id'] = $interview->getId();
        $jsonContent['idCandidatureOffre'] = $interview->getCandidatureOffre()->getId();
        $jsonContent['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
        $jsonContent['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
        $jsonContent['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
        $jsonContent['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
        $jsonContent['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
        $jsonContent['candidatureOffre'] = $interview->getCandidatureOffre()->getId();
        $jsonContent['difficulte'] = $interview->getDifficulte();
        $jsonContent['description'] = $interview->getDescription();
        $jsonContent['dateCreation'] = $interview->getDateCreation()->format('H:i - d/M/Y');

        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("manipuler_interview")
     * @throws Exception
     */
    public function manipulerInterview(Request $request)
    {
        $idInterview = (int)$request->get("id");

        if ($idInterview == null) {
            $interview = new Interview();
            $idCandidat = (int)$request->get("candidatId");
            $idOffre = (int)$request->get("offreDeTravailId");
            $interview->setCandidatureOffre(
                $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
                    "candidat" => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
                    "offreDeTravail" => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffre)
                ])
            );
        } else {
            $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);
        }

        $difficulte = (int)$request->get("difficulte");
        $description = $request->get("description");

        $interview
            ->setDifficulte($difficulte)
            ->setDescription($description)
            ->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($interview);
        $manager->flush();

        return new Response("Ajout/Modification effectué");
    }

    /**
     * @Route("supprimer_interview")
     * @param Request $request
     * @return Response
     */
    public function supprimerInterview(Request $request)
    {
        $idInterview = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Interview::class)->find($idInterview));
        $manager->flush();

        return new Response("Suppression effectué");
    }
}
