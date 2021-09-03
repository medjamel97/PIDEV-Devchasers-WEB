<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\Publication;
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
class PublicationController extends AbstractController
{

    /**
     * @Route("recuperer_publications")
     * @return Response
     */
    public function recupererPublications(): Response
    {
        $publications = $this->getDoctrine()->getRepository(Publication::class)->findAll();

        if (!$publications) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($publications as $publication) {
            $jsonContent[$i]['id'] = $publication->getId();
            $jsonContent[$i]['candidat_id'] = $publication->getUser()->getId();
            $jsonContent[$i]['titre'] = $publication->getTitre();
            $jsonContent[$i]['description'] = $publication->getDescription();
            $jsonContent[$i]['date'] = $publication->getDate()->format('Y-m-d H:i:s');
            $jsonContent[$i]['pourcentage_like'] = $publication->getPourcentageLike();

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_societe_offre_pour_publication")
     * @return Response
     */
    public function recupererSocietePourPublication(): Response
    {
        $societes = $this->getDoctrine()->getRepository(Societe::class)->findAll();
        $jsonContent = null;

        if (!$societes) {
            return new Response(null);
        }

        $i = 0;
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
     * @Route("manipuler_publication")
     * @throws Exception
     */
    public function manipulerPublication(Request $request): Response
    {
        $idPublication = (int)$request->get("id");

        if ($idPublication == null) {
            $publication = new Publication();
            $userId = (int)$request->get("userId");
            $idOffre = (int)$request->get("offreDeTravailId");
            $publication->setCandidatureOffre(
                $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
                    "user" => $this->getDoctrine()->getRepository(Candidat::class)->find($userId),
                    "offreDeTravail" => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffre)
                ])
            );
        } else {
            $publication = $this->getDoctrine()->getRepository(Publication::class)->find($idPublication);
        }

        $nbEtoiles = (int)$request->get("nbEtoiles");
        $objet = $request->get("objet");
        $description = $request->get("description");

        $publication
            ->setNbEtoiles($nbEtoiles)
            ->setObjet($objet)
            ->setDescription($description)
            ->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($publication);
        $manager->flush();

        return new Response("Ajout/Modification effectué");
    }

    /**
     * @Route("supprimer_publication")
     * @param Request $request
     * @return Response
     */
    public function supprimerPublication(Request $request): Response
    {
        $idPublication = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Publication::class)->find($idPublication));
        $manager->flush();

        return new Response("Suppression effectué");
    }

    /**
     * @Route("modifierpub", name="modifierpub")
     * @throws Exception
     */
    public function modifierPublication(Request $request): Response
    {
        $idPub = (int)$request->get("id");
        $userId = (int)$request->get("userId");
        $titre = $request->get("titre");
        $description = $request->get("description");
        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($idPub);
        $publication->setTitre($titre)->setDescription($description)->setDate($date);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($publication);
        $manager->flush();
        return new Response (null);

    }

    /**
     * @Route("majouterpub", name="majouterpub")
     */
    public function ajouterPublication(Request $request): Response
    {
        $idCandidat = (int)$request->get("candidatId");
        $titre = $request->get("titre");
        $description = $request->get("description");
        try {
            $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));
        } catch (Exception $e) {
        }


        $publication = new Publication();


        $publication->setCandidat(

            $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat)

        )->setTitre($titre)->setDescription($description)->setDate($date);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($publication);
        $manager->flush();
        return new Response (null);
    }

}
