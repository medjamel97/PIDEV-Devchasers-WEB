<?php

namespace App\Controller\back_end_societe;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_societe/")
 */
class CandidatController extends AbstractController
{
    /**
     * @Route("candidat")
     */
    public function afficherToutCandidat(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->render('back_end_societe/candidat/afficher_tout.html.twig', [
            'candidats' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Candidat::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("candidat/{idCandidat}/profil")
     */
    public function profile($idCandidat): Response
    {
        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat);
        $educations = $this->getDoctrine()->getRepository(Education::class)->findOneBySomeField($idCandidat);
        $experienceDeTravails = $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->findOneBySomeField($idCandidat);
        $competences = $this->getDoctrine()->getRepository(Competence::class)->findOneBySomeField($idCandidat);

        return $this->render('back_end_societe/candidat/profil.html.twig', [
            'candidat' => $candidat,
            'educations' => $educations,
            'experienceDeTravails' => $experienceDeTravails,
            'competences' => $competences
        ]);
    }

    /**
     * @Route("candidat/recherche")
     * @throws Exception
     */
    public function rechercheCandidat(Request $request): Response
    {
        $recherche = $request->get('recherche');

        $candidats = $this->getDoctrine()->getRepository(Candidat::class)->findStartingWith($recherche);

        $i = 0;
        $jsonContent = null;
        if ($candidats != null) {
            foreach ($candidats as $candidat) {
                $jsonContent[$i]["id"] = $candidat->getId();
                $jsonContent[$i]["nom"] = $candidat->getNom();
                $jsonContent[$i]["prenom"] = $candidat->getPrenom();
                $jsonContent[$i]["dateNaissance"] = $candidat->getDateNaissance()->format('d-m-Y');
                $jsonContent[$i]["sexe"] = $candidat->getSexe();
                $jsonContent[$i]["tel"] = $candidat->getTel();
                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }
}
