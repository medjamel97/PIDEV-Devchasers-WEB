<?php

namespace App\Controller\back_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CandidatController extends Controller
{
    /**
     * @Route("back_end/candidat", name="afficher_tout_candidat_back")
     */
    public function afficherToutCandidat(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $candidats = $em->getRepository(Candidat::class)->findAll();
        $paginator = $this->get('knp_paginator');
        $candidats = $paginator->paginate(
            $candidats,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 2)
        );
        return $this->render('back_end/candidat/afficher_tout.html.twig', [
            'candidats' => $candidats,
        ]);
    }

    /**
     * @Route("back_end/candidat/{idCandidat}/profil", name="profil_candidat_back")
     */
    public function profile($idCandidat)
    {
        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat);
        $educations = $this->getDoctrine()->getRepository(Education::class)->findOneBySomeField($idCandidat);
        $workexps = $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->findOneBySomeField($idCandidat);
        $competences = $this->getDoctrine()->getRepository(Competence::class)->findOneBySomeField($idCandidat);

        return $this->render("back_end/candidat/profil.html.twig", [
            'candidat' => $candidat,
            'educations' => $educations,
            'workexps' => $workexps,
            'competences' => $competences
        ]);
    }
}