<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\Revue;
use App\Entity\OffreDeTravail;
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
            $jsonContent[$i]['idCandidatureOffre'] = $revue->getCandidatureOffre()->getId();
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
        $idRevue = (int)$request->get("id");

        $revue = $this->getDoctrine()->getRepository(Revue::class)->find($idRevue);

        $jsonContent['id'] = $revue->getId();
        $jsonContent['idCandidatureOffre'] = $revue->getCandidatureOffre()->getId();
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
     * @Route("recuperer_societe_offre_pour_revue")
     * @return Response
     */
    public function recupererSocietePourRevue()
    {
        $societes = $this->getDoctrine()->getRepository(Societe::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $societe = new Societe();
        foreach ($societes as $societe) {
            $jsonContent[$i]['id'] = $societe->getId();
            $jsonContent[$i]['nom'] = $societe->getNom();
            $jsonContent[$i]['idPhoto'] = $societe->getIdPhoto();
            $jsonContent[$i]['tel'] = $societe->getTel();

            $j = 0;
            foreach ($societe->getOffreDeTravail() as $offreDeTravail) {
                $jsonContent[$i]['offres'][$j]['id'] = $offreDeTravail->getId();
                $jsonContent[$i]['offres'][$j]['nom'] = $offreDeTravail->getNom();
                $j++;
            }

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("manipuler_revue")
     * @throws Exception
     */
    public function manipulerRevue(Request $request)
    {
        $idRevue = (int)$request->get("id");

        if ($idRevue == null) {
            $revue = new Revue();
            $idCandidat = (int)$request->get("candidatId");
            $idOffre = (int)$request->get("offreDeTravailId");
            $revue->setCandidatureOffre(
                $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
                    "candidat" => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
                    "offreDeTravail" => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffre)
                ])
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

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($revue);
        $manager->flush();

        return new Response("Ajout/Modification effectué");
    }

    /**
     * @Route("supprimer_revue")
     * @param Request $request
     * @return Response
     */
    public function supprimerRevue(Request $request)
    {
        $idRevue = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Revue::class)->find($idRevue));
        $manager->flush();

        return new Response("Suppression effectué");
    }

}
