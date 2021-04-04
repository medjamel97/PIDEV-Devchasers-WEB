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
     * @Route("", name="")
     */
    public function index()
    {
        return $this->redirectToRoute('afficherToutPublication');
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil()
    {
        return $this->redirectToRoute('afficherToutPublication');
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

            $currentEmail = $utilisateurConnexion->getEmail();
            $currentMdp = $utilisateurConnexion->getMotDePasse();

            foreach ($utilisateurs as $utilisateur) {
                if ($currentEmail == $utilisateur->getEmail()) {
                    if ($currentMdp == $utilisateur->getMotDePasse()) {
                        $utilisateurConnexion = $manager->getRepository(Utilisateur::class)
                            ->connexion($currentEmail, $currentMdp);

                        if ($utilisateurConnexion->getTypeUtilisateur() == 0) {
                            $idCandidat = null;
                            $idSociete = $utilisateurConnexion->getSociete()->getId();
                        } else {
                            $idCandidat = $utilisateurConnexion->getCandidat()->getId();
                            $idSociete = null;
                        }

                        $this->session->set("utilisateur", [
                            'idUtilisateur' => $utilisateurConnexion->getId(),
                            'idCandidat' => $idCandidat,
                            'idSociete' => $idSociete,
                            'emailUtilisateur' => $utilisateur->getEmail(),
                            'typeUtilisateur' => $utilisateurConnexion->getTypeUtilisateur(),
                        ]);

                        return $this->redirectToRoute("accueil");
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
        return $this->redirectToRoute("afficherToutPublication");
    }
}