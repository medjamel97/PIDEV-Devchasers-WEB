<?php

namespace App\Controller\back_end_admin;

use App\Entity\Revue;
use App\Entity\User;
use App\Form\RevueType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_admin/")
 */
class RevueController extends AbstractController
{
    /**
     * @Route("revue")
     */
    public function afficherToutRevue()
    {
        return $this->render('/back_end_admin/revue/afficher_tout.html.twig', [
            'revues' => $this->getDoctrine()->getRepository(Revue::class)->findAll()
        ]);
    }

    /**
     * @Route("revue/ajouter")
     */
    public function ajouterRevue(Request $request)
    {
        return $this->manipulerRevue($request, 'Ajouter', new Revue());
    }

    /**
     * @Route("revue/{idRevue}/modifier")
     */
    public function modifierRevue(Request $request, $idRevue)
    {
        return $this->manipulerRevue($request, 'Modifier',
            $this->getDoctrine()->getRepository(Revue::class)->find($idRevue));
    }

    public function manipulerRevue(Request $request, $manipulation, $revue)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(RevueType::class, $revue)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $revue = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($revue);
                $entityManager->flush();

                return $this->redirect('/espace_admin/revue');
            }

            return $this->render('/back_end_admin/revue/manipuler.html.twig', [
                'revue' => $revue,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("revue/{idRevue}/supprimer")
     */
    public function supprimerRevue($idRevue)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Revue::class)->find($idRevue));
        $entityManager->flush();

        return $this->redirect('/espace_admin/revue');
    }
}
