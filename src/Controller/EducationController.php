<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Education;
use App\Form\EducationType;
use App\Repository\EducationRepository;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/education")
 */
class EducationController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="education_index", methods={"GET"})
     */
    public function index(EducationRepository $educationRepository): Response
    {
        return $this->render('education/index.html.twig', [
            'educations' => $educationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="education_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $education = new Education();
        $form = $this->createForm(EducationType::class, $education);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $form->getData();
            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $utilisateur->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($education);
            $entityManager->flush();

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('education/new.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="education_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Education $education): Response
    {
        $form = $this->createForm(EducationType::class, $education);
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

        return $this->render('education/edit.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="education_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Education $education): Response
    {
        if ($this->isCsrfTokenValid('delete' . $education->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($education);
            $entityManager->flush();
        }

        return $this->redirectToRoute('education_index');
    }
}
