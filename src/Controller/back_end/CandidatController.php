<?php

namespace App\Controller\back_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
class CandidatController extends AbstractController
{
    /**
     * @Route("candidat")
     */
    public function afficherToutCandidat(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('back_end/candidat/afficher_tout.html.twig', [
            'candidat' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Candidat::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("candidat/{idCandidat}/profil")
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