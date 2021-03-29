<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/categorie={idCategorie}", name="afficherCategorie")
     */
    public function afficherCategorie($idSociete,$idCategorie): Response
    {
        return null;
    }

    /**
     * @Route("/categorie", name="afficherToutCategorie")
     */
    public function afficherToutCategorie(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/categorie/afficherCategorie.html.twig', [
            'categories' => $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe/categorie/ajouter", name="ajouterCategorie")
     */
    public function ajouterCategorie(Request $request)
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categorie = $form->getData();

            $categorieRepository = $this->getDoctrine()->getManager();
            $categorieRepository->persist($categorie);
            $categorieRepository->flush();

            return $this->redirectToRoute('afficherCategorie');
        }

        return $this->render('/frontEnd/utilisateur/societe/categorie/manipulerCategorie.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/categorie={idCategorie}/modifier", name="modifierCategorie")
     */
    public function modifierCategorie(Request $request, $idCategorie)
    {
        $categorieRepository = $this->getDoctrine()->getManager();
        $categorie = $categorieRepository->getRepository(Categorie::class)->find($idCategorie);

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->flush();
            return $this->redirectToRoute('afficherCategorie');
        }

        return $this->render('/frontEnd/utilisateur/societe/categorie/manipulerCategorie.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/categorie={idCategorie}/supprimer", name="supprimerCategorie")
     */
    public function supprimerCategorie($idCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $categorie = $manager->getRepository(Categorie::class)->find($idCategorie);
        $manager->remove($categorie);
        $manager->flush();
        return $this->redirectToRoute('afficherCategorie');
    }
}
