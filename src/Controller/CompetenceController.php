<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Form\CompetenceType;
use App\Repository\CompetenceRepository;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/competence")
 */
class CompetenceController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/new", name="competence_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
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

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('frontEnd/utilisateur/candidat/competence/manipulationCompetence.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="competence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Competence $competence): Response
    {
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

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('frontEnd/utilisateur/candidat/competence/manipulationCompetence.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("/{id}", name="competence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Competence $competence): Response
    {
        if ($this->isCsrfTokenValid('delete' . $competence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($competence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('competence_index');
    }
}
