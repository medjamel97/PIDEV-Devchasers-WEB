<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\CandidatType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatController extends AbstractController
{
    /**
     * @Route("/candidat", name="afficherCandidat")
     */
    public function afficherCandidat()
    {
        return $this->render('frontend/candidat/afficher.html.twig', [
            'candidats' => $this->getDoctrine()->getRepository(Societe::class)->findAll(),
        ]);
    }

    /**
     * @Route("/candidat/ajouter/email={email}/password={motDePasse}", name="ajouterCandidat")
     */
    public function ajouterCandidat(Request $request,$email,$motDePasse)
    {
        $utilisateur = new Utilisateur();
        $candidat = new Candidat();

        $form = $this->createForm(CandidatType::class, $candidat)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(0);

            $candidat = $form->getData()
                ->setUtilisateur($utilisateur);

            $utilisateurManager = $this->getDoctrine()->getManager();
            $utilisateurManager->persist($utilisateur);
            $utilisateurManager->flush();

            $candidatManager = $this->getDoctrine()->getManager();
            $candidatManager->persist($candidat);
            $candidatManager->flush();

            return $this->redirectToRoute('afficherUtilisateur');
        }

        return $this->render('frontend/candidat/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
