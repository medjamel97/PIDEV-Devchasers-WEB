<?php

namespace App\Controller\Mobile;

use App\Entity\CandidatureOffre;
use App\Entity\Revue;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class RevueController extends AbstractController
{

    /**
     * @Route("recuperer_revues")
     * @return Response
     */
    public function recupererRevues()
    {
        $revues = $this->getDoctrine()->getRepository(Revue::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $revue = new Revue();
        foreach ($revues as $revue) {
            $jsonContent[$i]['id'] = $revue->getId();
            $jsonContent[$i]['nomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getNom();
            $jsonContent[$i]['prenomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getPrenom();
            $jsonContent[$i]['idPhotoCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getIdPhoto();
            $jsonContent[$i]['nomSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
            $jsonContent[$i]['nomOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getNom();
            $jsonContent[$i]['nbEtoiles'] = $revue->getNbEtoiles();
            $jsonContent[$i]['objet'] = $revue->getObjet();
            $jsonContent[$i]['description'] = $revue->getDescription();
            $jsonContent[$i]['dateCreation'] = $revue->getDateCreation()->format('Y-m-d H:i:s');
            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_revue_par_id")
     * @param Request $request
     * @return Response
     */
    public function recupererRevueParId(Request $request)
    {
        $idRevue = (int)$request->get("idRevue");

        $revue = $this->getDoctrine()->getRepository(Revue::class)->find($idRevue);

        $jsonContent['id'] = $revue->getId();
        $jsonContent['nomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getNom();
        $jsonContent['prenomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getPrenom();
        $jsonContent['idPhotoCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getIdPhoto();
        $jsonContent['nomSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
        $jsonContent['nomOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getNom();
        $jsonContent['candidatureOffre'] = $revue->getCandidatureOffre()->getId();
        $jsonContent['nbEtoiles'] = $revue->getNbEtoiles();
        $jsonContent['objet'] = $revue->getObjet();
        $jsonContent['description'] = $revue->getDescription();
        $jsonContent['dateCreation'] = $revue->getDateCreation()->format('Y-m-d H:i:s');

        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("manipuler_revue")
     * @param Request $request
     * @return Response
     */
    public function manipulerRevue(Request $request)
    {
        try {
            $idRevue = (int)$request->get("idRevue");

            if ($idRevue == null) {
                $revue = new Revue();
                $candidatureOffre = (int)$request->get("candidatureOffre");
                $revue->setCandidatureOffre(
                    $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($candidatureOffre)
                );
            } else {
                $revue = $this->getDoctrine()->getRepository(Revue::class)->find($idRevue);
            }

            $nbEtoiles = (int)$request->get("nbEtoiles");
            $objet = $request->get("objet");
            $description = $request->get("description");

            $revue
                ->setNbEtoiles($nbEtoiles)
                ->setObjet($objet)
                ->setDescription($description)
                ->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));
            try {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($revue);
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
     * @Route("supprimer_revue")
     * @param Request $request
     * @return Response
     */
    public function supprimerRevue(Request $request)
    {
        try {
            $idRevue = (int)$request->get("idRevue");

            $manager = $this->getDoctrine()->getManager();
            $manager->remove($this->getDoctrine()->getRepository(Revue::class)->find($idRevue));
            $manager->flush();

            return new Response(0);
        } catch (\Exception $e) {
            return new Response(-1);
        }
    }
}
