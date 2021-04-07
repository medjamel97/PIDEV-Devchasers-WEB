<?php

namespace App\Controller\front_end;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Entity\Like;
use App\Form\PublicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("publication", name="afficher_tout_publication")
     */
    public function afficherToutPublication(): Response
    {
        return $this->render('front_end/candidat/publication/afficher_tout.html.twig', [
            'publications' => $this->getDoctrine()->getRepository(Publication::class)->findAll(),
            'commentaires' => $this->getDoctrine()->getManager()->getRepository(Commentaire::class)->findAll(),
        ]);
    }

    /**
     * @Route("publication/{idPublication}/afficher", name="afficher_publication")
     */
    public function afficherPublication($idPublication)
    {
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository(Publication::class)->find($idPublication);
        $nombreLikesTrue = $em->getRepository(Like::class)->nombreObjets($idPublication);
        $nombreLikes = $em->getRepository(Like::class)->nombreLikes($idPublication);


        return $this->render('front_end/candidat/publication/afficher_tout.html.twig', [
            'publication' => $publication,
            'nombreLikesTrue' => $nombreLikesTrue,
            'nombreLikes' => $nombreLikes,
            'commentaires' => $this->getDoctrine()->getManager()
                ->getRepository(Commentaire::class)->findBy(['publication' => $publication]),
        ]);
    }

    /**
     * @Route("publication/ajouter", name="ajouter_publication")
     */
    public function ajouterPublication(Request $request)
    {
        // Connexion candidat requise
        $session = $this->session->get("utilisateur");
        if ($session == null) {
            return $this->redirectToRoute('connexion');
        } else {
            if ($session['idCandidat'] == null){
                return $this->redirectToRoute('connexion');
            }
        }

        $publication = new Publication();

        $form = $this->createForm(PublicationType::class, $publication)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $publication = $form->getData();

            $publicationRepository = $this->getDoctrine()->getManager();
            $publicationRepository->persist($publication);
            $publicationRepository->flush();

            return $this->redirectToRoute('afficher_tout_publication');
        }

        return $this->render('front_end/candidat/publication/manipuler.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter",
        ]);
    }

    /**
     * @Route("publication/{idPublication}/modifier", name="modifier_publication")
     */
    public function modifierPublication(Request $request, $idPublication)
    {
        // Connexion candidat requise
        $session = $this->session->get("utilisateur");
        if ($session == null) {
            return $this->redirectToRoute('connexion');
        } else {
            if ($session['idCandidat'] == null){
                return $this->redirectToRoute('connexion');
            }
        }

        $publicationRepository = $this->getDoctrine()->getManager();
        $publication = $publicationRepository->getRepository(Publication::class)->find($idPublication);

        $form = $this->createForm(PublicationType::class, $publication);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->flush();
            return $this->redirectToRoute('afficher_tout_publication');
        }

        return $this->render('front_end/candidat/publication/manipuler.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("publication/{idPublication}/supprimer", name="supprimer_publication")
     */
    public function supprimerPublication($idPublication)
    {
        $publicationManager = $this->getDoctrine()->getManager();
        $publication = $publicationManager->getRepository(Publication::class)->find($idPublication);
        $publicationManager->remove($publication);
        $publicationManager->flush();
        return $this->redirectToRoute('gererPublication');
    }

    /**
     * @Route("publication/rechreche ", name="chercher_publication")
     * @throws ExceptionInterface
     */
    public function searchPub(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("searchValue");
        $titre = $this->getDoctrine()->getRepository(Publication::class)->findStudentByTitre($recherche);
        $jsonContent = $normalizer->normalize($titre, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

}



    



