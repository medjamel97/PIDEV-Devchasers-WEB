<?php


namespace App\Controller\Mobile;


use App\Entity\Categorie;
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

        if (!$offresDeTravail) {
            return new Response(null);
        }

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
     * @Route("recuperer_offre_par_societe")
     */
    public function recupererOffreParSociete(Request $request): Response
    {
        $societeId = $request->get("societeId");

        $societe = $this->getDoctrine()->getRepository(Societe::class)->find($societeId);

        if (!$societe) {
            return new Response(null);
        }

        $offresDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->findBy(["societe" => $societe]);

        if (!$offresDeTravail) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($offresDeTravail as $offreDeTravail) {
            $jsonContent[$i]['id'] = $offreDeTravail->getId();
            $jsonContent[$i]['societeId'] = $offreDeTravail->getSociete()->getId();
            $jsonContent[$i]['nom'] = $offreDeTravail->getNom();
            $jsonContent[$i]['description'] = $offreDeTravail->getDescription();
            $jsonContent[$i]['nomSociete'] = $offreDeTravail->getSociete()->getNom();

            if ($offreDeTravail->getCategorie()) {
                $jsonContent[$i]['categorieId'] = $offreDeTravail->getCategorie()->getId();
                $jsonContent[$i]['nomCategorie'] = $offreDeTravail->getCategorie()->getNom();
            } else {
                $jsonContent[$i]['categorieId'] = 0;
                $jsonContent[$i]['nomCategorie'] = "aucune categorie";
            }

            $i++;
        }

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("ajouter_offre")
     */
    public function ajouterOffre(Request $request): Response
    {
        $nom = $request->get('nom');
        $description = $request->get('description');
        $societeId = $request->get('societeId');

        $offre = new OffreDeTravail();
        $offre->setNom($nom);
        $offre->setDescription($description);
        $offre->setCategorie($this->getDoctrine()->getRepository(Categorie::class)->find(1));
        $offre->setSociete($this->getDoctrine()->getRepository(Societe::class)->find($societeId));
        $offre->setSalaire("1000");

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
