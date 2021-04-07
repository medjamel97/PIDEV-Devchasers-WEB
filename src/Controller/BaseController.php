<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("")
     * @Route("front_end", name="front_end")
     * @Route("accueil", name="accueil")
     */
    public function accueil()
    {
        return $this->redirectToRoute('afficher_tout_publication');
    }

    /**
     * @Route("back_end", name="back_end")
     */
    public function backEnd()
    {
        return $this->redirectToRoute('afficher_tout_publication_back_end');
    }

    /**
     * @Route("connexion", name="connexion")
     */
    public function connexion(Request $request)
    {
        $loginUser = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $loginUser)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();

            $currentEmail = $loginUser->getEmail();
            $currentPassword = $loginUser->getMotDePasse();

            foreach ($utilisateurs as $utilisateur) {
                if ($currentEmail == $utilisateur->getEmail()) {
                    if ($currentPassword == $utilisateur->getMotDePasse()) {
                        $utilisateurConnexion = $manager->getRepository(Utilisateur::class)
                            ->connexion($currentEmail, $currentPassword);

                        if ($utilisateurConnexion->getTypeUtilisateur() == 0) {
                            $idCandidat = null;
                            $idSociete = $utilisateurConnexion->getSociete()->getId();
                        } else {
                            $idSociete = $utilisateurConnexion->getCandidat()->getId();
                            $idCandidat = null;
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
     * @Route("deconnexion", name="deconnexion")
     */
    public function deconnexion()
    {
        $this->session->set("utilisateur", null);
        return $this->redirectToRoute("accueil");
    }
}