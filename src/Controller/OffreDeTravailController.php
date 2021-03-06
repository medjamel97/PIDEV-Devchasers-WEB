<?php

namespace App\Controller;

use App\Entity\OffreDeTravail;
use App\Form\OffreDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("/societe/categorie/offreDeTravail", name="afficherOffreDeTravail")
     */
    public function afficherOffreDeTravail(): Response
    {
        return null;
    }

    /**
     * @Route("/societe={societeID}/categorie={categorieID}/offreDeTravail", name="afficherToutOffreDeTravail")
     */
    public function afficherToutOffreDeTravail($societeID, $categorieID): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/afficherOffreDeTravail.html.twig', [
            'offreDeTravails' => $this->getDoctrine()->getManager()->getRepository(OffreDeTravail::class)->findBy([
                'societe' => $societeID,
            ]),
            'societe' => $societeID,
        ]);
    }

    /**
     * @Route("/societe={societeID}/categorie/offreDeTravail/ajouter", name="ajouterOffreDeTravail")
     */
    public function ajouterOffreDeTravail(Request $request, $societeID)
    {
        $offreDeTravail = new OffreDeTravail();

        $form = $this->createForm(OffreDeTravailType::class, $offreDeTravail)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $offreDeTravail = $form->getData();


            $offreDeTravailRepository = $this->getDoctrine()->getManager();
            $offreDeTravailRepository->persist($offreDeTravail);
            $offreDeTravailRepository->flush();

            return $this->redirectToRoute('afficherToutOffreDeTravail', [
                'societeID' => $societeID,
                'categorieID' => "0",
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/manipulerOffreDeTravail.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter",
        ]);
    }

    /**
     * @Route("/societe={societeID}/categorie/offreDeTravail={idOffreDeTravail}/modifier", name="modifierOffreDeTravail")
     */
    public function modifierOffreDeTravail(Request $request, $societeID, $idOffreDeTravail)
    {
        $offreDeTravailRepository = $this->getDoctrine()->getManager();
        $offreDeTravail = $offreDeTravailRepository->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

        $form = $this->createForm(OffreDeTravailType::class, $offreDeTravail);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offreDeTravailRepository->flush();
            return $this->redirectToRoute('afficherToutOffreDeTravail', [
                'societeID' => $societeID,
                'categorieID' => "0",
            ]);
        }

        return $this->render('/frontEnd/utilisateur/societe/offreDeTravail/manipulerOffreDeTravail.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe={societeID}/categorie/offreDeTravail={idOffreDeTravail}/supprimer", name="supprimerOffreDeTravail")
     */
    public function supprimerOffreDeTravail($societeID,$idOffreDeTravail)
    {
        $offreDeTravailManager = $this->getDoctrine()->getManager();
        $offreDeTravail = $offreDeTravailManager->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);
        $offreDeTravailManager->remove($offreDeTravail);
        $offreDeTravailManager->flush();
        return $this->redirectToRoute('afficherToutOffreDeTravail', [
            'societeID' => $societeID,
            'categorieID' => "0",
        ]);
    }
}
