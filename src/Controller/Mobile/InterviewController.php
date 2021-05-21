<?php /** @noinspection DuplicatedCode */

namespace App\Controller\Mobile;

use App\Entity\CandidatureOffre;
use App\Entity\Interview;
use App\Entity\Societe;
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
    public function recupererInterviews(): Response
    {
        $interviews = $this->getDoctrine()->getRepository(Interview::class)->findAll();

        if ($interviews == null) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($interviews as $interview) {
            $jsonContent[$i]['id'] = $interview->getId();
            $jsonContent[$i]['idCandidatureOffre'] = $interview->getCandidatureOffre()->getId();
            $jsonContent[$i]['idCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getId();
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
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("recuperer_interview_par_id")
     * @param Request $request
     * @return Response
     */
    public function recupererInterviewParId(Request $request): Response
    {
        $idInterview = (int)$request->get("id");

        $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);

        if ($interview == null) {
            return new Response(null);
        }

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
     * @Route("recuperer_societe_offre_pour_interview")
     * @return Response
     */
    public function recupererSocietePourInterview(): Response
    {
        $societes = $this->getDoctrine()->getRepository(Societe::class)->findAll();

        if ($societes == null) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        $societe = new Societe();
        foreach ($societes as $societe) {
            $jsonContent[$i]['idSociete'] = $societe->getId();
            $jsonContent[$i]['nomSociete'] = $societe->getNom();
            $jsonContent[$i]['idPhotoSociete'] = $societe->getIdPhoto();
            $jsonContent[$i]['telSociete'] = "T" . $societe->getTel();

            $j = 0;
            foreach ($societe->getOffreDeTravail() as $offreDeTravail) {
                $jsonContent[$i]['offres'][$j]['idOffre'] = $offreDeTravail->getId();
                $jsonContent[$i]['offres'][$j]['nomOffre'] = $offreDeTravail->getNom();
                $j++;
            }

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("manipuler_interview")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function manipulerInterview(Request $request): Response
    {
        $idInterview = (int)$request->get("id");
        $idCandidatureOffre = (int)$request->get("idCandidatureOffre");

        if ($idInterview == 0) {
            $interview = new Interview();
            $interview->setCandidatureOffre($this->getDoctrine()->getRepository(
                CandidatureOffre::class)->find($idCandidatureOffre)
            );
            $interview->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));
        } else {
            $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);
        }

        $difficulte = (int)$request->get("difficulte");
        $description = $request->get("description");

        $interview
            ->setDifficulte($difficulte)
            ->setDescription($description);

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
    public function supprimerInterview(Request $request): Response
    {
        $idInterview = (int)$request->get("id");


        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Interview::class)->find($idInterview));
        $manager->flush();

        return new Response("Suppression effectué");
    }

}
