<?php

namespace App\Controller\back_end_admin;

use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("formation")
     */
    public function afficherToutFormation()
    {
        return $this->render('admin/formation/afficher_tout.html.twig', [
            'formations' => $this->getDoctrine()->getRepository(Formation::class)->findAll()
        ]);
    }

    /**
     * @Route("formation/ajouter")
     */
    public function ajouterFormation(Request $request)
    {
        return $this->manipulerFormation($request, 'Ajouter', new Formation());
    }

    /**
     * @Route("formation/{idFormation}/modifier")
     */
    public function modifierFormation(Request $request, $idFormation)
    {
        return $this->manipulerFormation($request, 'Modifier',
            $this->getDoctrine()->getRepository(Formation::class)->find($idFormation));
    }

    public function manipulerFormation(Request $request, $manipulation, $formation)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(FormationType::class, $formation)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $formation = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($formation);
                $entityManager->flush();

                return $this->redirect('/admin/formation');
            }

            return $this->render('/admin/formation/manipuler.html.twig', [
                'formation' => $formation,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("formation/{idFormation}/supprimer")
     */
    public function supprimerFormation($idFormation)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Formation::class)->find($idFormation));
        $entityManager->flush();

        return $this->redirect('/admin/formation');
    }
}
