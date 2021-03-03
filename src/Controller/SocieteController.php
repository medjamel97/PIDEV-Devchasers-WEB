<?php

namespace App\Controller;

use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\SocieteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    /**
     * @Route("/societe", name="afficher")
     */
    public function afficherSociete()
    {
        return $this->render('frontend/societe/afficher.html.twig', [
            'societes' => $this->getDoctrine()->getRepository(Societe::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe/ajouter/email={email}/password={motDePasse}", name="ajouterSociete")
     */
    public function ajouterSociete(Request $request,$email,$motDePasse)
    {
        $utilisateur = new Utilisateur();
        $societe = new Societe();

        $form = $this->createForm(SocieteType::class, $societe)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(0);

            $societe = $form->getData()
                ->setUtilisateur($utilisateur);

            $utilisateurManager = $this->getDoctrine()->getManager();
            $utilisateurManager->persist($utilisateur);
            $utilisateurManager->flush();

            $societeManager = $this->getDoctrine()->getManager();
            $societeManager->persist($societe);
            $societeManager->flush();

            return $this->redirectToRoute('afficherUtilisateur');
        }

        return $this->render('frontend/societe/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/societe/modifier/id={id}", name="modifierSociete")
     */
    public function modifierSociete(Request $request,$id)
    {
        $SocieteRepository = $this->getDoctrine()->getManager();
        $societe = $SocieteRepository->getRepository(Societe::class)->find($id);

        $form = $this->createForm(SocieteType::class, $societe);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $SocieteRepository->flush();
            return $this->redirectToRoute('afficherSociete');
        }

        return $this->render('/frontend/societe/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
