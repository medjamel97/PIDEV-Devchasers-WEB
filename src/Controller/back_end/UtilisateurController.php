<?php

namespace App\Controller\back_end;

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
     * @Route("utilisateur/id={idUtilisateur}/modifier", name="modifier_utilisateur")
     */
    public function modifierUtilisateur(Request $request, $idUtilisateur)
    {
        $utilisateurRepository = $this->getDoctrine()->getManager();
        $utilisateur = $utilisateurRepository->getRepository(Utilisateur::class)->find($id);

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('submit', SubmitType::class);
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

        return $this->render('back_end/utilisateur/modifier.html.twig', [
            'form' => $form->createView(),
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
