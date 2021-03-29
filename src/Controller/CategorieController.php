<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class CategorieController extends AbstractController
{
    /**
     * @Route("/ajouCategorie", name="ac")
     */
    public function addCat(Request $request)
    {
        $cat = new Categorie();

        $form = $this->createForm(CategorieType::class, $cat)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();


            return $this->redirectToRoute('listc');
        }
        return $this->render('/backEnd/ajouCategorie.html.twig', [
            'form' => $form->createView(),
            'Cat' => $this->getDoctrine()->getRepository(Categorie::class)->findAll()

        ]);
    }

    /**
     * @Route("/deletecat/{id}", name="dele")
     */
    public function delCat(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $e = $em->getRepository(Categorie::class)->find($id);
        $em->remove($e);
        $em->flush();

        return $this->redirectToRoute('listc');
    }

    /**
     * @Route("/modif/{id}", name="modcat")
     */
    public function ModifierCat(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Categorie::class)->find($id);

        $form = $this->createForm(CategorieType::class, $cat)
            ->add('Modifier', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listc');
        }

        return $this->render('/backEnd/ajouCategorie.html.twig', ['form' => $form->createView(),
            'Cat' => $this->getDoctrine()->getRepository(Categorie::class)->findAll()
        ]);
    }

    /**
     * @Route("/listcat", name="listc")
     */
    public function LireCat(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Categorie::class)->findAll();


        return $this->render('/backEnd/listcat.html.twig', [
            'Cat' => $cat
        ]);
    }


}
