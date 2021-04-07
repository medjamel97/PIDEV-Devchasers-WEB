<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Form\CompetenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompetenceController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("competence/ajouter", name="ajouter_competence")
     */
    public function ajouterCompetence(Request $request)
    {
        $competence = new Competence();
        $form = $this->createForm(CompetenceType::class, $competence)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $competence = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $competence->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($competence);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/competence/manipulation.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter'
        ]);
    }

    /**
     * @Route("competence/{idCompetence}/modifier", name="modifier_competence")
     */
    public function modifierCompetence(Request $request, $idCompetence)
    {
        $competence = $this->getDoctrine()->getRepository(Competence::class)->find($idCompetence);

        $form = $this->createForm(CompetenceType::class, $competence)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $education = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $education->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($education);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/competence/manipulation.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("competence/{idCompetence}/supprimer", name="supprimer_competence")
     */
    public function supprimerCompetence($idCompetence)
    {
        $manager = $this->getDoctrine()->getManager();
        $competence = $manager->getRepository(Competence::class)->find($idCompetence);
        $manager->remove($competence);
        $manager->flush();

        return $this->redirectToRoute('profil_candidat');
    }
}
