<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Form\CandidatureOffreType;
use App\Repository\CandidatureOffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureOffreController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("candidatureOffre/offreDeTravail={idOffreDeTravail}/ajouter", name="ajouterCandidatureOffre")
     */
    public function ajouterCandidatureOffre($idOffreDeTravail): Response
    {
        $candidatureOffre = new CandidatureOffre();
        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
            $this->session->get("utilisateur")["idCandidat"]
        );
        $offreDeTravail = $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);
        $candidatureOffre->setCandidat($candidat);
        $candidatureOffre->setOffreDeTravail($offreDeTravail);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($candidatureOffre);
        $entityManager->flush();

        return $this->redirectToRoute('afficherToutOffres');
    }

    /**
     * @Route("candidatureOffre/{id}/supprimer", name="supprimerCandidatureOffre")
     */
    public
    function delete(Request $request, CandidatureOffre $candidatureOffre): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidatureOffre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidatureOffre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidatureOffre_index');
    }
}
