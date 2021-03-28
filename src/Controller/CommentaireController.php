<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/candidat={}/publication={}/commentaire={}", name="afficherCommentaire")
     */
    public function afficherCommentaire(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/commentaire/afficherCommentaire.html.twig', [
            'commentaires' => $this->getDoctrine()->getManager()->getRepository(Commentaire::class)->findAll(),
        ]);
    }

    /**
     * @Route("/commentaire", name="afficherToutCommentaire")
     */
    public function afficherToutCommentaire(): Response
    {
        return null;
    }

    /**
     * @Route("/candidat/publication/commentaire/ajouter", name="ajouterCommentaire")
     */
    public function ajouterCommentaire(Request $request)
    {
        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentaire = $form->getData();

            $commentaireRepository = $this->getDoctrine()->getManager();
            $commentaireRepository->persist($commentaire);
            $commentaireRepository->flush();

            return $this->redirectToRoute('afficherCommentaire');
        }

        return $this->render('/frontEnd/utilisateur/societe/commentaire/manipulerCommentaire.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/publication/commentaire={idCommentaire}/modifier", name="modifierCommentaire")
     */
    public function modifierCommentaire(Request $request, $idCommentaire)
    {
        $commentaireRepository = $this->getDoctrine()->getManager();
        $commentaire = $commentaireRepository->getRepository(Commentaire::class)->find($idCommentaire);

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->flush();
            return $this->redirectToRoute('afficherCommentaire');
        }

        return $this->render('/frontEnd/utilisateur/societe/commentaire/manipulerCommentaire.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/candidat/publication/commentaire={idCommentaire}/supprimer", name="supprimerCommentaire")
     */
    public function supprimerCommentaire($idCommentaire)
    {
        $commentaireManager = $this->getDoctrine()->getManager();
        $commentaire = $commentaireManager->getRepository(Commentaire::class)->find($idCommentaire);
        $commentaireManager->remove($commentaire);
        $commentaireManager->flush();
        return $this->redirectToRoute('afficherCommentaire');
    }
}
