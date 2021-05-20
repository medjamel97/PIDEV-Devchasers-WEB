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
    public function recupererPublications()
    {
        $publications = $this->getDoctrine()->getRepository(Publication::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $publication = new Publication();
        foreach ($publications as $publication) {
            $jsonContent[$i]['id'] = $publication->getId();
            $jsonContent[$i]['candidat_id'] = $publication->getCandidat()->getId();
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
     * @Route("recuperer_publication_par_id")
     * @param Request $request
     * @return Response
     */
    public function recupererPublicationParId(Request $request)
    {
        $idPublication = (int)$request->get("id");

        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($idPublication);

        $jsonContent['id'] = $publication->getId();
        $jsonContent['idCandidatureOffre'] = $publication->getCandidatureOffre()->getId();
        $jsonContent['nomCandidat'] = $publication->getCandidatureOffre()->getCandidat()->getNom();
        $jsonContent['prenomCandidat'] = $publication->getCandidatureOffre()->getCandidat()->getPrenom();
        $jsonContent['idPhotoCandidat'] = $publication->getCandidatureOffre()->getCandidat()->getIdPhoto();
        $jsonContent['nomSociete'] = $publication->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
        $jsonContent['nomOffre'] = $publication->getCandidatureOffre()->getOffreDeTravail()->getNom();
        $jsonContent['candidatureOffre'] = $publication->getCandidatureOffre()->getId();
        $jsonContent['nbEtoiles'] = $publication->getNbEtoiles();
        $jsonContent['objet'] = $publication->getObjet();
        $jsonContent['description'] = $publication->getDescription();
        $jsonContent['dateCreation'] = $publication->getDateCreation()->format('Y-m-d H:i:s');

        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_societe_offre_pour_publication")
     * @return Response
     */
    public function recupererSocietePourPublication()
    {
        $societes = $this->getDoctrine()->getRepository(Societe::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $societe = new Societe();
        foreach ($societes as $societe) {
            $jsonContent[$i]['idSociete'] = $societe->getId();
            $jsonContent[$i]['nomSociete'] = $societe->getNom();
            $jsonContent[$i]['idPhotoSociete'] = $societe->getIdPhoto();
            $jsonContent[$i]['telSociete'] = "T".$societe->getTel();

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
    public function manipulerPublication(Request $request)
    {
        $idPublication = (int)$request->get("id");

        if ($idPublication == null) {
            $publication = new Publication();
            $idCandidat = (int)$request->get("candidatId");
            $idOffre = (int)$request->get("offreDeTravailId");
            $publication->setCandidatureOffre(
                $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
                    "candidat" => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
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
    public function supprimerPublication(Request $request)
    {
        $idPublication = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Publication::class)->find($idPublication));
        $manager->flush();

        return new Response("Suppression effectué");
    }

    /**
     * @Route("modifierpub", name="modifierpub")
     */
    public function modifierPublication(Request $request)
    {$idPub = (int)$request->get("id");
        $idCandidat = (int)$request->get("candidatId");
        $titre = $request->get("titre");
        $description = $request->get("description");
        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));        
            $publication = $this->getDoctrine()->getRepository(Publication::class)->find($idPub) ;     
            $publication->setTitre($titre)->setDescription($description)->setDate($date);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($publication);
            $manager->flush();
            return new Response (null) ;
    
    }

/**
     * @Route("majouterpub", name="majouterpub")
     */
    public function ajouterPublication(Request $request)
    {
        $idCandidat = (int)$request->get("candidatId");
        $titre = $request->get("titre");
        $description = $request->get("description");
        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));

        
            $publication = new Publication();
          
         
            $publication->setCandidat(

             $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat)          
             
            )->setTitre($titre)->setDescription($description)->setDate($date);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($publication);
            $manager->flush();
            return new Response (null) ;
    }

}
