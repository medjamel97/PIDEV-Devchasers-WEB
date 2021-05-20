<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class ExperienceDeTravailController extends AbstractController
{
    /**
     * @Route("recuperer_experience_de_travail")
     */
    public function recupererExperienceDeTravail(Request $request): Response
    {
        $candidatId = (int)$request->get('candidatId');

        $experiences_de_travail = $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->findBy([
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId)
        ]);

        if (!$experiences_de_travail){
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($experiences_de_travail as $experience_de_travail) {
            $jsonContent[$i]['id'] = $experience_de_travail->getId();
            $jsonContent[$i]['description'] = $experience_de_travail->getDescription();
            $jsonContent[$i]['titreEmploi'] = $experience_de_travail->getTitreEmploi();
            $jsonContent[$i]['nomEntreprise'] = $experience_de_travail->getNomEntreprise();
            $jsonContent[$i]['ville'] = $experience_de_travail->getVille();
            $jsonContent[$i]['duree'] = $experience_de_travail->getDuree();
            $jsonContent[$i]['idCandidat'] = $experience_de_travail->getCandidat()->getId();

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("ajouter_experience_de_travail")
     */
    public function ajouterExperienceDeTravail(Request $request)
    {
        $candidatId = (int)$request->get('candidatId');
        $description = $request->get('description');
        $titreEmploi = $request->get('titreEmploi');
        $nomEntreprise = $request->get('nomEntreprise');
        $ville = $request->get('ville');
        $duree = $request->get('duree');


        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId);

        $experience_de_travail = new ExperienceDeTravail();

        $experience_de_travail->setCandidat($candidat)
            ->setDescription($description)
            ->setNomEntreprise($nomEntreprise)
            ->setTitreEmploi($titreEmploi)
            ->setVille($ville)
            ->setDuree($duree);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($experience_de_travail);
        $entityManager->flush();

        return (new Response(null));
    }

    /**
     * @Route("supprimer_experience_de_travail")
     */
    public function supprimerExperienceDeTravail(Request $request)
    {
        $idExperienceDeTravail = (int)$request->get("id");
        $manager = $this->getDoctrine()->getManager();
        $experienceDeTravail = $manager->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);
        $manager->remove($experienceDeTravail);
        $manager->flush();
        return new Response("Suppression effectué");

    }
}
