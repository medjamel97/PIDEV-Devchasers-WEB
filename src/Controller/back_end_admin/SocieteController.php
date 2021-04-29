<?php

namespace App\Controller\back_end_admin;

use App\Entity\Societe;
use App\Entity\User;
use App\Form\SocieteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class SocieteController extends AbstractController
{
    /**
     * @Route("societe")
     */
    public function afficherToutSociete()
    {
        return $this->render('admin/societe/afficher_tout.html.twig', [
            'societes' => $this->getDoctrine()->getRepository(Societe::class)->findAll()
        ]);
    }

    /**
     * @Route("societe/ajouter")
     */
    public function ajouterSociete(Request $request)
    {
        return $this->manipulerSociete($request, 'Ajouter', new Societe());
    }

    /**
     * @Route("societe/{idSociete}/modifier")
     */
    public function modifierSociete(Request $request, $idSociete)
    {
        return $this->manipulerSociete($request, 'Modifier',
            $this->getDoctrine()->getRepository(Societe::class)->find($idSociete));
    }

    public function manipulerSociete(Request $request, $manipulation, $societe)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(SocieteType::class, $societe)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $societe = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($societe);
                $entityManager->flush();

                return $this->redirect('/admin/societe');
            }

            return $this->render('/admin/societe/manipuler.html.twig', [
                'societe' => $societe,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("societe/{idSociete}/supprimer")
     */
    public function supprimerSociete($idSociete)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Societe::class)->find($idSociete));
        $entityManager->flush();

        return $this->redirect('/admin/societe');
    }
}
