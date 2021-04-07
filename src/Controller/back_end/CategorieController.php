<?php

namespace App\Controller\back_end;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("back_end/categorie", name="afficher_tout_categorie")
     */
    public function afficherToutCategorie()
    {
        return $this->render('back_end/societe/offre_de_travail/categorie/afficher_tout.html.twig', [
            'Cat' => $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll(),
        ]);
    }

    /**
     * @Route("back_end/categorie/ajouter", name="ajouter_categorie")
     */
    public function ajouterCategorie(Request $request)
    {
        $cat = new Categorie();

        $form = $this->createForm(CategorieType::class, $cat)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();


            return $this->redirectToRoute('afficher_tout_categorie');
        }
        return $this->render('back_end/societe/offre_de_travail/categorie/manipuler.html.twig', [
            'form' => $form->createView(),
            'Cat' => $this->getDoctrine()->getRepository(Categorie::class)->findAll()

        ]);
    }

    /**
     * @Route("back_end/categorie/{idCategorie}/modifier", name="modifier_categorie")
     */
    public function modifierCategorie(Request $request, $idCategorie)
    {
        $categorie = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->find($idCategorie);

        $form = $this->createForm(CategorieType::class, $categorie)
            ->add('Modifier', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirectToRoute('afficher_tout_categorie');
        }

        return $this->render('/back_end/ajouCategorie.html.twig', [
            'form' => $form->createView(),
            'Cat' => $this->getDoctrine()->getRepository(Categorie::class)->findAll()
        ]);
    }

    /**
     * @Route("back_end/categorie/{idCategorie}/supprimer", name="supprimer_categorie")
     */
    public function supprimerCategorie($idCategorie)
    {
        $em = $this->getDoctrine()->getManager();
        $e = $em->getRepository(Categorie::class)->find($idCategorie);
        $em->remove($e);
        $em->flush();

        return $this->redirectToRoute('afficher_tout_categorie');
    }
}
