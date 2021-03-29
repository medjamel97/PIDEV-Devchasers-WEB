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
     * @Route("/new", name="education_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $education = new Education();
        $form = $this->createForm(EducationType::class, $education)
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

        return $this->render('frontEnd/utilisateur/candidat/education/manipulationEducation.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="education_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Education $education): Response
    {
        $form = $this->createForm(EducationType::class, $education)
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

        return $this->render('frontEnd/utilisateur/candidat/education/manipulationEducation.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
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
