<?php

namespace App\Controller;

use App\Entity\ExperienceDeTravail;
use App\Form\ExperienceDeTravailType;
use App\Repository\ExperienceDeTravailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/experience/de/travail")
 */
class ExperienceDeTravailController extends AbstractController
{
    /**
     * @Route("/", name="experience_de_travail_index", methods={"GET"})
     */
    public function index(ExperienceDeTravailRepository $experienceDeTravailRepository): Response
    {
        return $this->render('experience_de_travail/index.html.twig', [
            'experience_de_travails' => $experienceDeTravailRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="experience_de_travail_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $experienceDeTravail = new ExperienceDeTravail();
        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail)
            ->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($experienceDeTravail);
            $entityManager->flush();

            return $this->redirectToRoute('experience_de_travail_index');
        }

        return $this->render('experience_de_travail/new.html.twig', [
            'experience_de_travail' => $experienceDeTravail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="experience_de_travail_show", methods={"GET"})
     */
    public function show(ExperienceDeTravail $experienceDeTravail): Response
    {
        return $this->render('experience_de_travail/show.html.twig', [
            'experience_de_travail' => $experienceDeTravail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="experience_de_travail_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ExperienceDeTravail $experienceDeTravail): Response
    {
        $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('experience_de_travail_index');
        }

        return $this->render('experience_de_travail/edit.html.twig', [
            'experience_de_travail' => $experienceDeTravail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="experience_de_travail_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ExperienceDeTravail $experienceDeTravail): Response
    {
        if ($this->isCsrfTokenValid('delete'.$experienceDeTravail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($experienceDeTravail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('experience_de_travail_index');
    }
}
