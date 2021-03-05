<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    /**
     * @Route("/candidat={}/publication={}", name="afficherPublication")
     */
    public function afficherPublication(): Response
    {
        return $this->render('/frontEnd/utilisateur/candidat/publication/afficherPublication.html.twig', [
            'publications' => $this->getDoctrine()->getManager()->getRepository(Publication::class)->findAll(),
        ]);
    }

    /**
     * @Route("/publication", name="afficherToutPublication")
     */
    public function afficherToutPublication(): Response
    {
        return $this->render('/frontEnd/utilisateur/candidat/publication/afficherPublication.html.twig', [
            'publications' => $this->getDoctrine()->getManager()->getRepository(Publication::class)->findAll(),
        ]);
    }

    /**
     * @Route("/candidat/publication/ajouter", name="ajouterPublication")
     */
    public function ajouterPublication(Request $request)
    {
        $publication = new Publication();

        $form = $this->createForm(PublicationType::class, $publication)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $publication = $form->getData();

            $publicationRepository = $this->getDoctrine()->getManager();
            $publicationRepository->persist($publication);
            $publicationRepository->flush();

            return $this->redirectToRoute('afficherPublication');
        }

        return $this->render('/frontEnd/utilisateur/candidat/publication/manipulerPublication.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/publication={idPublication}/modifier", name="modifierPublication")
     */
    public function modifierPublication(Request $request, $idPublication)
    {
        $publicationRepository = $this->getDoctrine()->getManager();
        $publication = $publicationRepository->getRepository(Publication::class)->find($idPublication);

        $form = $this->createForm(PublicationType::class, $publication);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->flush();
            return $this->redirectToRoute('afficherPublication');
        }

        return $this->render('/frontEnd/utilisateur/candidat/publication/manipulerPublication.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/publication={idPublication}/supprimer", name="supprimerPublication")
     */
    public function supprimerPublication($idPublication)
    {
        $publicationManager = $this->getDoctrine()->getManager();
        $publication = $publicationManager->getRepository(Publication::class)->find($idPublication);
        $publicationManager->remove($publication);
        $publicationManager->flush();
        return $this->redirectToRoute('afficherPublication');
    }
}
