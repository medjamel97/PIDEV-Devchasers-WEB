<?php

namespace App\Controller\back_end_societe;

use App\Entity\Categorie;
use App\Entity\User;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_societe/")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("categorie")
     */
    public function afficherToutCategorie()
    {
        return $this->render('back_end_societe/societe/offre_de_travail/categorie/afficher_tout.html.twig', [
            'categories' => $this->getDoctrine()->getRepository(Categorie::class)->findAll()
        ]);
    }

    /**
     * @Route("categorie/{idCategorie}/afficher")
     */
    public function afficherCategorie($idCategorie)
    {
        return $this->render('back_end_societe/societe/offre_de_travail/categorie/afficher.html.twig', [
            'categories' => $this->getDoctrine()->getRepository(Categorie::class)->find($idCategorie)
        ]);
    }

    /**
     * @Route("categorie/recherche")
     * @throws ExceptionInterface
     */
    public function rechercheCategorie(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("valeurRecherche");
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findOneByCategorieName($recherche);

        $jsonContent = $normalizer->normalize($categorie, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    /**
     * @Route("categorie/ajouter")
     */
    public function ajouterCategorie(Request $request)
    {
        return $this->manipulerCategorie($request, 'Ajouter', new Categorie());
    }

    /**
     * @Route("categorie/{idCategorie}/modifier")
     */
    public function modifierCategorie(Request $request, $idCategorie)
    {
        return $this->manipulerCategorie($request, 'Modifier',
            $this->getDoctrine()->getRepository(Categorie::class)->find($idCategorie));
    }

    public function manipulerCategorie(Request $request, $manipulation, $categorie)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(CategorieType::class, $categorie)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $categorie = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($categorie);
                $entityManager->flush();

                return $this->redirect('/back_end_societe/categorie');
            }

            return $this->render('back_end_societe/societe/offre_de_travail/categorie/manipuler.html.twig', [
                'categorie' => $categorie,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("categorie/{idCategorie}/supprimer")
     */
    public
    function supprimerCategorie($idCategorie)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($idCategorie);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirect('/back_end_societe/categorie');
    }
}
