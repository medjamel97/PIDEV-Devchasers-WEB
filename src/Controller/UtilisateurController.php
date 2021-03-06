<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="afficherUtilisateur")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/utilisateur/afficherUtilisateur.html.twig', [
            'utilisateurs' => $this->getDoctrine()->getRepository(Utilisateur ::class)->findAll(),
        ]);
    }

    /**
     * @Route("/utilisateur/ajouter", name="ajouterUtilisateur")
     */
    public function ajouterUtilisateur(Request $request)
    {
        $utilisateur = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $utilisateur)
            ->add('Suivant', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $form->getData();

            if ($utilisateur->getTypeUtilisateur() == 0){
                return $this->redirectToRoute("ajouterSociete", [
                    'email'  => $utilisateur->getEmail(),
                    'motDePasse'  => $utilisateur->getMotDePasse(),
                ]);
            }elseif($utilisateur->getTypeUtilisateur() == 1){
                return $this->redirectToRoute("ajouterCandidat", [
                    'email'  => $utilisateur->getEmail(),
                    'motDePasse'  => $utilisateur->getMotDePasse(),
                ]);
            }else{
                $this->redirectToRoute("acceuil");
            }
        }

        return $this->render('frontEnd/utilisateur/manipulerUtilisateur.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter"
        ]);
    }

    /**
     * @Route("/utilisateur/modifier/id={id}", name="modifierUtilisateur")
     */
    public function modifierUtilisateur(Request $request,$id)
    {
        $utilisateurRepository = $this->getDoctrine()->getManager();
        $utilisateur = $utilisateurRepository->getRepository(Utilisateur::class)->find($id);

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateurRepository->flush();
            return $this->redirectToRoute('afficherUtilisateur');
        }

        return $this->render('frontEnd/utilisateur/modifierUtilisateur.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }

    /**
     * @Route("/utilisateur/supprimer/id={id}", name="supprimerUtilisateur")
     */
    public function supprimerUtilisateur($id): Response
    {
        $utilisateurManager = $this->getDoctrine()->getManager();
        $utilisateur = $utilisateurManager->getRepository(Utilisateur::class)->find($id);
        $utilisateurManager->remove($utilisateur);
        $utilisateurManager->flush();
        return $this->redirectToRoute('afficherUtilisateur');
    }
}
