<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\User;
use App\Form\CompetenceType;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("mobile/")
 */
class CompetenceController extends AbstractController
{
    /**
     * @Route("recuperer_competence")
     * @return
     */
    public function recupererCompetences(Request $request): Response
    {
        $candidatId = (int)$request->get('candidatId');

        $competences = $this->getDoctrine()->getRepository(Competence::class)->findBy([
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId)
        ]);

        if (!$competences){
            throw new Error("Vide");
        }

        $jsonContent = null;
        $i = 0;
        foreach ($competences as $competence) {
            $jsonContent[$i]['id'] = $competence->getId();
            $jsonContent[$i]['name'] = $competence->getName();
            $jsonContent[$i]['level'] = $competence->getLevel();
            $jsonContent[$i]['idCandidat'] = $competence->getCandidat()->getId();

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("ajouter_competence")
     */
    public function ajouterCompetence(Request $request)
    {
        $candidatId = (int)$request->get('candidatId');
        $level = (int)$request->get('level');
        $name = $request->get('name');

        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId);

        $competence = new Competence();

        $competence->setCandidat($candidat)->setLevel($level)->setName($name);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($competence);
        $entityManager->flush();

        return (new Response(null));
    }

    /**
     * @Route("modifier_competence")
     */
    public function modifierCompetence(Request $request)
    {
        $idCompetence = (int)$request->get("id");
        $competence = $this->getDoctrine()->getRepository(Competence::class)->find($idCompetence);

        $level = (int)$request->get('level');
        $name = $request->get('name');
        $competence->setLevel($level)->setName($name);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($competence);
        $entityManager->flush();

        return new Response("Modification effectué");
    }

    /**
     * @Route("supprimer_competence")
     */
    public function supprimerCompetence(Request $request)
    {
        $idCompetence = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $competence = $manager->getRepository(Competence::class)->find($idCompetence);
        $manager->remove($competence);
        $manager->flush();
        return new Response("Suppression effectué");
    }
}
