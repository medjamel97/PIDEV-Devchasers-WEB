<?php

namespace App\Controller\front_end;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Entity\Like;
use App\Entity\User;
use App\Form\PublicationType;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    /**
     * @Route("accueil", name="accueil")
     */
    public function afficherToutPublication(): Response
    {
        return $this->render('front_end/publication/afficher_tout.html.twig', [
            'publications' => $this->getDoctrine()->getRepository(Publication::class)->findBy([], [
                'date' => 'DESC'
            ]),
            'commentaires' => $this->getDoctrine()->getManager()->getRepository(Commentaire::class)->findAll(),
        ]);
    }

    /**
     * @Route("publication/{idPublication}/afficher", name="afficher_publication")
     */
    public function afficherPublication($idPublication): Response
    {
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository(Publication::class)->find($idPublication);
        $nombreLikesTrue = $em->getRepository(Like::class)->nombreObjets($idPublication);
        $nombreLikes = $em->getRepository(Like::class)->nombreLikes($idPublication);

        return $this->render('front_end/publication/afficher_tout.html.twig', [
            'publication' => $publication,
            'nombreLikesTrue' => $nombreLikesTrue,
            'nombreLikes' => $nombreLikes,
            'commentaires' => $this->getDoctrine()->getManager()
                ->getRepository(Commentaire::class)->findBy(['publication' => $publication]),
        ]);
    }

    /**
     * @Route("publication/ajouter", name="ajouter_publication")
     * @throws Exception
     */
    public function ajouterPublication(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $publication = new Publication();

        $form = $this->createForm(PublicationType::class, $publication)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $publication = $form->getData();
            $publication->setUser($user);
            $publication->setDate(new DateTime('now', new DateTimeZone('Africa/Tunis')));

            $publicationRepository = $this->getDoctrine()->getManager();
            $publicationRepository->persist($publication);
            $publicationRepository->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('front_end/publication/manipuler.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter une",
        ]);
    }

    /**
     * @Route("publication/{idPublication}/modifier", name="modifier_publication")
     */
    public function modifierPublication(Request $request, $idPublication): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $publicationRepository = $this->getDoctrine()->getManager();
        $publication = $publicationRepository->getRepository(Publication::class)->find($idPublication);

        $form = $this->createForm(PublicationType::class, $publication);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->flush();
            return $this->redirectToRoute('accueil');
        }

        return $this->render('front_end/publication/manipuler.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier votre",
        ]);
    }

    /**
     * @Route("publication/{idPublication}/supprimer", name="supprimer_publication")
     */
    public function supprimerPublication($idPublication): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $publicationManager = $this->getDoctrine()->getManager();
        $publication = $publicationManager->getRepository(Publication::class)->find($idPublication);
        $publicationManager->remove($publication);
        $publicationManager->flush();
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("publication/rechreche ", name="recherche_publication")
     * @throws ExceptionInterface
     */
    public function recherchePublication(Request $request, NormalizerInterface $normalizer): Response
    {
        $recherche = $request->get("valeur-recherche");
        $titre = $this->getDoctrine()->getRepository(Publication::class)->findStudentByTitre($recherche);
        $jsonContent = $normalizer->normalize($titre, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

}



    



