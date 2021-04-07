<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Societe;
use App\Entity\Utilisateur;
use App\Form\CandidatType;
use App\Form\SocieteType;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class InscriptionController extends AbstractController
{
    /**
     * @Route("inscription", name="inscription")
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
                return $this->inscriptionSociete($utilisateur->getEmail(), $utilisateur->getMotDePasse());
            } elseif ($utilisateur->getTypeUtilisateur() == 1) {

                //$this->getRequest()->setParameter('email', $utilisateur->getEmail());
                return $this->forward('App\Controller\InscriptionController:inscriptionCandidat', [
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                ]);

                //return $this->forward(, $utilisateur->getEmail(), $utilisateur->getMotDePasse());
            } else {
                $this->redirectToRoute("acceuil");
            }
        }

        return $this->render('_inscription/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("inscription/candidat", name="inscription_candidat")
     */
    public function inscriptionCandidat(Request $request)
    {
        $email = $request->get('email');
        $motDePasse = $request->get('motDePasse');
        $this->addFlash('success', $request->get('email'));
        $this->addFlash('success', $request->get('motDePasse'));
        $this->addFlash('success', $request->set);



        $utilisateur = new Utilisateur();
        $candidat = new Candidat();

        $form = $this->createForm(CandidatType::class, $candidat)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(1);
            $candidat = $form->getData()
                ->setUtilisateur($utilisateur);

            $file = $request->files->get('candidat')['idPhoto'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $candidat->setIdPhoto($filename);

            $utilisateurManager = $this->getDoctrine()->getManager();
            $utilisateurManager->persist($utilisateur);
            $utilisateurManager->flush();

            $candidatManager = $this->getDoctrine()->getManager();
            $candidatManager->persist($candidat);
            $candidatManager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('_inscription/inscrire_candidat.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("inscription/societe", name="inscription_societe")
     */
    public function inscriptionSociete($email, $motDePasse)
    {
        $request = new Request();
        $utilisateur = new Utilisateur();
        $societe = new Societe();

        $form = $this->createForm(SocieteType::class, $societe)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $utilisateur->setEmail($email)
                ->setMotDePasse($motDePasse)
                ->setTypeUtilisateur(0);

            $societe = $form->getData()
                ->setUtilisateur($utilisateur);

            $file = $request->files->get('societe')['idPhotoSociete'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $societe->setIdPhotoSociete($filename);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($utilisateur);
            $manager->flush();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($societe);
            $manager->flush();

            return $this->redirectToRoute('connexion');
        }

        return $this->render('_inscription/inscrire_societe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
