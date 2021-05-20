<?php


namespace App\Controller\Mobile;


use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("mobile/")
 */
class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("recuperer_offres")
     * @return Response
     */
    public function recupererOffres(): Response
    {
        $offresDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->findAll();
        $jsonContent = null;
        $i = 0;
        foreach ($offresDeTravail as $offreDeTravail) {
            $jsonContent[$i]['id'] = $offreDeTravail->getId();
            $jsonContent[$i]['nom'] = $offreDeTravail->getNom();
            $jsonContent[$i]['description'] = $offreDeTravail->getDescription();
            if ($offreDeTravail->getCategorie()) {
                $jsonContent[$i]['nomCategorie'] = $offreDeTravail->getCategorie()->getNom();
            } else {
                $jsonContent[$i]['nomCategorie'] = "aucune categorie";
            }

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
    public function recupererOffreDeTravailParId(Request $request): Response
    {
        $idOffreDeTravail = (int)$request->get("id");

        $revue = $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

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
     * @Route("recuperer_offre_par_societe")
     */
    public function recupererOffreParSociete(Request $request): Response
    {
        $societeId = $request->get("societeId");

        $societe = $this->getDoctrine()->getRepository(Societe::class)->find($societeId);

        $offresDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy(["societe" => $societe]);

        $jsonContent = null;
        $i = 0;
        foreach ($offresDeTravail as $offreDeTravail) {
            $jsonContent[$i]['id'] = $offreDeTravail->getId();
            $jsonContent[$i]['nom'] = $offreDeTravail->getNom();
            $jsonContent[$i]['description'] = $offreDeTravail->getDescription();
            if ($offreDeTravail->getCategorie()) {
                $jsonContent[$i]['nomCategorie'] = $offreDeTravail->getCategorie()->getNom();
            } else {
                $jsonContent[$i]['nomCategorie'] = "aucune categorie";
            }

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("ajouter_offre")
     */
    public function ajouterOffre(): Response
    {
        $offre = new OffreDeTravail();
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($offre);
        $manager->flush();
        return new Response("Ajout effectué");
    }

    /**
     * @Route("modifier_offre")
     */
    public function modifierOffre(Request $request): Response
    {
        $idOffre = (int)$request->get("id");
        $offre = $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffre);
        $nom = $request->get("nom");
        $description = $request->get("description");

        $offre
            ->setNom($nom)
            ->setDescription($description);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($offre);
        $manager->flush();

        return new Response("Modification effectué");
    }

    /**
     * @Route("supprimer_offre")
     * @param Request $request
     * @return Response
     */
    public function supprimerOffreDeTravail(Request $request): Response
    {
        $idOffreDeTravail = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail));
        $manager->flush();

        return new Response("Suppression effectué");
    }


}
