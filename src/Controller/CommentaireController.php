<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Entity\Utilisateur;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("commentaire/ajouter", name="ajouterCommentaire")
     */
    public function ajouterCommentaire(Request $request)
    {
        $idUtilisateur = $this->session->get("utilisateur")["idUtilisateur"];

        $commentaire = new Commentaire();
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($request->get('idPublication'));
        $commentaire
            ->setDescription($request->get('description'))
            ->setPublication($publication)
            ->setUtilisateur($this->getDoctrine()->getRepository(Utilisateur::class)->find($idUtilisateur));

        $commentaireRepository = $this->getDoctrine()->getManager();
        $commentaireRepository->persist($commentaire);
        $commentaireRepository->flush();

        $jsonContent['id'] = $commentaire->getId();
        $jsonContent['nomPrenom'] =
            $commentaire->getUtilisateur()->getCandidat()->getPrenom() . " " .
            $commentaire->getUtilisateur()->getCandidat()->getNom() . " :";
        $jsonContent['idPhoto'] = $commentaire->getUtilisateur()->getCandidat()->getIdPhoto();
        $jsonContent['description'] = $commentaire->getDescription();

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("commentaire/{idCommentaire}/modifier", name="modifierCommentaire")
     */
    public function modifierCommentaire(Request $request, $idCommentaire)
    {
        $modifiedCommentDescription = $request->get('modifiedComment');
        $commentaireRepository = $this->getDoctrine()->getManager();
        $commentaire = $commentaireRepository->getRepository(Commentaire::class)->find($idCommentaire);
        if ($modifiedCommentDescription != '') {
            $commentaire->setDescription($modifiedCommentDescription);
        }
        $commentaireRepository->flush();

        $jsonContent['id'] = $commentaire->getId();
        $jsonContent['nomPrenom'] =
            $commentaire->getUtilisateur()->getCandidat()->getPrenom() . " " .
            $commentaire->getUtilisateur()->getCandidat()->getNom() . " :";
        $jsonContent['idPhoto'] = $commentaire->getUtilisateur()->getCandidat()->getIdPhoto();
        $jsonContent['description'] = $commentaire->getDescription();

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("commentaire/{idCommentaire}/supprimer", name="supprimerCommentaire")
     */
    public function supprimerCommentaire($idCommentaire)
    {
        $commentaireManager = $this->getDoctrine()->getManager();
        $commentaire = $commentaireManager->getRepository(Commentaire::class)->find($idCommentaire);
        $commentaireManager->remove($commentaire);
        $commentaireManager->flush();

        return new Response(null);
    }
}