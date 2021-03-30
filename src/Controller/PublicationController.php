<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Entity\Like;
use App\Form\PublicationType;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PublicationController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/publication", name="afficherToutPublication")
     */
    public function afficherToutPublication(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository(Publication::class)->findAll();

        //  $idCandidat = $this->session->get("utilisateur")["idCandidat"];
        //  $pubs=$em->getRepository(Publication::class)->find($idCandidat);


        $nbrlike = $em->getRepository(Like::class)->countlikeNumber();
        $like = $em->getRepository(Like::class)->countItemNumber();
        if ($like != 0) {
            $pourcentage = ((float)$nbrlike / (float)$like) * 100;
        } else {
            $pourcentage = 0;
        }
        $paginator = $this->get('knp_paginator');
        $res = $paginator->paginate(
            $pubs,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );


        return $this->render('/frontEnd/utilisateur/candidat/publication/afficherPublication.html.twig', [
            'publications' => $res,
            'nbrlike' => $nbrlike,
            'like' => $like,
            'pourcen' => $pourcentage,
            'commentaires' => $this->getDoctrine()->getManager()->getRepository(Commentaire::class)->findAll(),
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

        if ($form->isSubmitted()) {

            $publication = $form->getData();

            $publicationRepository = $this->getDoctrine()->getManager();
            $publicationRepository->persist($publication);
            $publicationRepository->flush();

            return $this->redirectToRoute('afficherToutPublication');
        }

        return $this->render('/frontEnd/utilisateur/candidat/publication/manipulerPublication.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter",
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
            return $this->redirectToRoute('afficherToutPublication');
        }

        return $this->render('/frontEnd/utilisateur/candidat/publication/manipulerPublication.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }


    /**
     * @Route("/modifiercommentaire{idcommentaire}", name="modifierCommentaire")
     */
    public function modifierCommentaire(Request $request, $idcommentaire)
    {
        $commentaireRepository = $this->getDoctrine()->getManager();
        $commentaire = $commentaireRepository->getRepository(Commentaire::class)->find($idcommentaire);
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->flush();
            return $this->redirectToRoute('afficherToutPublication');
        }

        return $this->render('/frontEnd/utilisateur/candidat/publication/commentaire/manipulerCommentaire.html.twig', [
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
        return $this->redirectToRoute('afficherToutPublication ');
    }


    /**
     * @Route("/ajoutercommentaire", name="ajouterCommentaire", methods={"POST"})
     */
    public function ajouterCommentaire(Request $request)
    {
        $commentaire = new Commentaire();

        $commentaire->setDescription($request->get('description'));
        $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($request->get('id_publication'));
        $commentaire->setPublication($publication);

        $CommentaireRepository = $this->getDoctrine()->getManager();
        $CommentaireRepository->persist($commentaire);
        $CommentaireRepository->flush();

        return $this->redirectToRoute('afficherToutPublication');


    }

    /**
     * @Route("/supprimerCommentaire/{id}", name="supprimerCommentaire")
     */
    public function supprimerCommentaire($id)
    {
        $commentaireManager = $this->getDoctrine()->getManager();
        $commentaire = $commentaireManager->getRepository(Commentaire::class)->find($id);
        $commentaireManager->remove($commentaire);
        $commentaireManager->flush();
        return $this->redirectToRoute('afficherToutPublication');
    }

    /**
     * @Route("/afficherpublication/{id}", name="afficherPublication")
     */
    public function afficherunePublication($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $pubs = $em->getRepository(Publication::class)->find($id);
        $nbrlike = $em->getRepository(Like::class)->countlikeNumber();
        $like = $em->getRepository(Like::class)->countItemNumber();


        return $this->render('/frontEnd/utilisateur/candidat/publication/unepublication.html.twig', [
            'publication' => $pubs,
            'nbrlike' => $nbrlike,
            'like' => $like,

            'commentaires' => $this->getDoctrine()->getManager()->getRepository(Commentaire::class)->findAll(),


        ]);
    }

    /**
     * @Route("/searchPub ", name="searchPub")
     */
    public function searchPub(Request $request, NormalizerInterface $normalizer)
    {
        /*
    $repository = $this->getDoctrine()->getRepository(Publication::class);
    $requestString=$request->get('searchValue');
    $publications = $repository->findStudentByNsc($requestString);
    $jsonContent = $Normalizer->normalize($publications, 'json',['groups'=>'publications']);
    $retour=json_encode($jsonContent);
    return new Response($retour);
    */
        $recherche = $request->get("searchValue");
        //$mission=$this->getDoctrine()->getRepository(Mission::class)->findBy(['mission_name'=>$recherche]);
        $titre = $this->getDoctrine()->getRepository(Publication::class)->findStudentByTitre($recherche);
        $jsonContent = $normalizer->normalize($titre, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);


    }


}



    



