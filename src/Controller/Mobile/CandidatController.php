<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureMission;
use App\Entity\CandidatureOffre;
use App\Entity\Mission;
use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use DateTime;
use DateTimeZone;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class CandidatController extends AbstractController
{

    /**
     * @Route("recuperer_mes_candidats")
     * @return Response
     */
    public function recupererMesCandidats(Request $request): Response
    {
        $societeId = (int)$request->get('societeId');

        $candidatureOffres = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findBy([
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy([
                'societe' => $this->getDoctrine()->getRepository(Societe::class)->find(
                    $societeId
                )
            ])
        ]);

        $jsonContent = null;
        $i = 0;

        if (!$candidatureOffres) {
            return new Response(null);
        }

        foreach ($candidatureOffres as $candidatureOffre) {
            $candidat = $candidatureOffre->getCandidat();

            $jsonContent[$i]['id'] = $candidat->getId();
            $jsonContent[$i]['nom'] = $candidat->getNom();
            $jsonContent[$i]['prenom'] = $candidat->getPrenom();
            $jsonContent[$i]['dateNaissance'] = $candidat->getDateNaissance()->format('d-m-Y');
            $jsonContent[$i]['sexe'] = $candidat->getSexe();
            $jsonContent[$i]['tel'] = $candidat->getTel();
            $jsonContent[$i]['idPhoto'] = $candidat->getIdPhoto();
            $i++;
        }

        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_candidats")
     * @return Response
     */
    public function recupererCandidats(Request $request): Response
    {

        $jsonContent = null;
        $i = 0;

        $candidats = $this->getDoctrine()->getRepository(Candidat::class)->findAll();

        if (!$candidats) {
            return new Response(null);
        }

        foreach ($candidats as $candidat) {
            $jsonContent[$i]['id'] = $candidat->getId();
            $jsonContent[$i]['nom'] = $candidat->getNom();
            $jsonContent[$i]['prenom'] = $candidat->getPrenom();
            $jsonContent[$i]['dateNaissance'] = $candidat->getDateNaissance()->format('d-m-Y');
            $jsonContent[$i]['sexe'] = $candidat->getSexe();
            $jsonContent[$i]['tel'] = $candidat->getTel();
            $jsonContent[$i]['idPhoto'] = $candidat->getIdPhoto();
            $i++;
        }

        $json = json_encode($jsonContent);
        return new Response($json);
    }


    /**
     * @Route("supprimer_candidat")
     * @param Request $request
     * @return Response
     */
    public function supprimerCandidat(Request $request): Response
    {
        $idCandidat = (int)$request->get("candidatId");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat));
        $manager->flush();

        return new Response("Suppression effectué");
    }

}
