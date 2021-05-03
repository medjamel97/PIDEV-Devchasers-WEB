<?php

namespace App\Controller\Mobile;

use App\Entity\CandidatureOffre;
use App\Entity\Interview;
use DateTime;
use DateTimeZone;
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
            $jsonContent[$i]['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
            $jsonContent[$i]['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
            $jsonContent[$i]['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
            $jsonContent[$i]['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
            $jsonContent[$i]['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
            $jsonContent[$i]['difficulte'] = $interview->getDifficulte();
            $jsonContent[$i]['objet'] = $interview->getObjet();
            $jsonContent[$i]['description'] = $interview->getDescription();
            $jsonContent[$i]['dateCreation'] = $interview->getDateCreation()->format('Y-m-d H:i:s');
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
        $idInterview = (int)$request->get("idInterview");

        $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);

        $jsonContent['id'] = $interview->getId();
        $jsonContent['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
        $jsonContent['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
        $jsonContent['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
        $jsonContent['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
        $jsonContent['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
        $jsonContent['candidatureOffre'] = $interview->getCandidatureOffre()->getId();
        $jsonContent['difficulte'] = $interview->getDifficulte();
        $jsonContent['objet'] = $interview->getObjet();
        $jsonContent['description'] = $interview->getDescription();
        $jsonContent['dateCreation'] = $interview->getDateCreation()->format('Y-m-d H:i:s');

        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("manipuler_interview")
     * @param Request $request
     * @return Response
     */
    public function manipulerInterview(Request $request)
    {
        try {
            $idInterview = (int)$request->get("idInterview");

            if ($idInterview == null) {
                $interview = new Interview();
                $candidatureOffre = (int)$request->get("candidatureOffre");
                $interview->setCandidatureOffre(
                    $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($candidatureOffre)
                );
            } else {
                $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);
            }

            $difficulte = (int)$request->get("difficulte");
            $objet = $request->get("objet");
            $description = $request->get("description");

            $interview
                ->setDifficulte($difficulte)
                ->setObjet($objet)
                ->setDescription($description)
                ->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));
            try {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($interview);
                $manager->flush();
                return new Response(0);
            } catch (\Exception $e) {
                return new Response(1);
            }

        } catch (\Error $e) {
            return new Response(-1);
        } catch (\Exception $e) {
            return new Response(9);
        }
    }

    /**
     * @Route("supprimer_interview")
     * @param Request $request
     * @return Response
     */
    public function supprimerInterview(Request $request)
    {
        try {
            $idInterview = (int)$request->get("idInterview");

            $manager = $this->getDoctrine()->getManager();
            $manager->remove($this->getDoctrine()->getRepository(Interview::class)->find($idInterview));
            $manager->flush();

            return new Response(0);
        } catch (\Exception $e) {
            return new Response(-1);
        }
    }
}
