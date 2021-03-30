<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(Request $request)
    {
        $utilisateurConnexion = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $utilisateurConnexion)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();

            foreach ($utilisateurs as $utilisateur) {
                $email = $utilisateur->getEmail();
                if ($utilisateurConnexion->getEmail() == $email) {
                    $motDePasse = $utilisateur->getMotDePasse();
                    if ($utilisateurConnexion->getMotDePasse() == $motDePasse) {
                        $utilisateurConnexion = $manager->getRepository(Utilisateur::class)->connexion($email, $motDePasse);
                        if ($utilisateurConnexion->getTypeUtilisateur()==1) {
                            $this->session->set("utilisateur", [
                                'idUtilisateur' => $utilisateurConnexion->getId(),
                                'emailUtilisateur' => $utilisateurConnexion->getEmail(),
                                'idCandidat' => $utilisateurConnexion->getCandidat()->getId(),
                            ]);
                            return $this->redirectToRoute("afficherPublication");
                        } else {
                            $this->session->set("utilisateur", [
                                'idUtilisateur' => $utilisateurConnexion->getId(),
                                'emailUtilisateur' => $utilisateurConnexion->getEmail(),
                                'idSociete' => $utilisateurConnexion->getSociete()->getId(),
                            ]);
                            return $this->redirectToRoute("gererpublication");
                        }
                    }
                }
            }
        }

        return $this->render('_connexion/connexion.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function deconnexion()
    {
        $this->session->set("utilisateur", null);
        return $this->redirectToRoute("publication");
    }
}