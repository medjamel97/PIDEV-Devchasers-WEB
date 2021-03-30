<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @Route("/inscription", name="inscription")
     */
    public function inscription(Request $request)
    {
        $utilisateur = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $utilisateur)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $utilisateur = $form->getData();

            if ($utilisateur->getTypeUtilisateur() == 0) {
                return $this->redirectToRoute("ajouterSociete", [
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                ]);
            } elseif ($utilisateur->getTypeUtilisateur() == 1) {
                return $this->redirectToRoute("ajouterCandidat", [
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                ]);
            } else {
                $this->redirectToRoute("acceuil");
            }
        }

        return $this->render('_inscription/inscription.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Ajouter"
        ]);
    }

    /**
     * @Route("/utilisateur={idUtilisateur}/modifier", name="modifierUtilisateur")
     */
    public function modifierUtilisateur(Request $request, $idUtilisateur)
    {
        $utilisateurRepository = $this->getDoctrine()->getManager();
        $utilisateur = $utilisateurRepository->getRepository(Utilisateur::class)->find($idUtilisateur);

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur = $form->getData();

            if ($utilisateur->getTypeUtilisateur() == 0) {
                return $this->redirectToRoute("ajouterSociete", [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                ]);
            } elseif ($utilisateur->getTypeUtilisateur() == 1) {
                return $this->redirectToRoute("ajouterCandidat", [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                ]);
            } else {
                $this->redirectToRoute("acceuil");
            }
        }

        return $this->render('frontEnd/utilisateur/_inscription.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }

    /**
     * @Route("/utilisateur={idUtilisateur}/supprimer", name="supprimerUtilisateur")
     */
    public function supprimerUtilisateur($idUtilisateur): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $utilisateur = $manager->getRepository(Utilisateur::class)->find($idUtilisateur);
        $manager->remove($utilisateur);
        $manager->flush();
        return $this->redirectToRoute('afficherUtilisateur');
    }
}
