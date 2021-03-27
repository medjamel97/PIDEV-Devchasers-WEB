<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\ExperienceDeTravail;
use App\Form\ExperienceDeTravailType;
use App\Repository\ExperienceDeTravailRepository;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/experienceDeTravail")
 */
class ExperienceDeTravailController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="experienceDeTravail_index", methods={"GET"})
     */
    public function index(ExperienceDeTravailRepository $experienceDeTravailRepository): Response
    {
        return $this->render('experience_de_travail/index.html.twig', [
            'experienceDeTravails' => $experienceDeTravailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="experienceDeTravail_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $experienceDeTravail = new ExperienceDeTravail();
        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $form->getData();
            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $utilisateur->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($experienceDeTravail);
            $entityManager->flush();

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('experience_de_travail/new.html.twig', [
            'experienceDeTravail' => $experienceDeTravail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="experienceDeTravail_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ExperienceDeTravail $experienceDeTravail): Response
    {
        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $form->getData();
            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $utilisateur->setCandidat($candidat);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('experience_de_travail/edit.html.twig', [
            'experienceDeTravail' => $experienceDeTravail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="experienceDeTravail_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ExperienceDeTravail $experienceDeTravail): Response
    {
        if ($this->isCsrfTokenValid('delete' . $experienceDeTravail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($experienceDeTravail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('experienceDeTravail_index');
    }
}
