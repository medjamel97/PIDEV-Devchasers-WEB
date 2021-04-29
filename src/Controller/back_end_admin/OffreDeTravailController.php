<?php

namespace App\Controller\back_end_admin;

use App\Entity\OffreDeTravail;
use App\Entity\User;
use App\Form\OffreDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("offre_de_travail")
     */
    public function afficherToutOffreDeTravail()
    {
        return $this->render('admin/offre_de_travail/afficher_tout.html.twig', [
            'offreDeTravails' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->findAll()
        ]);
    }

    /**
     * @Route("offre_de_travail/ajouter")
     */
    public function ajouterOffreDeTravail(Request $request)
    {
        return $this->manipulerOffreDeTravail($request, 'Ajouter', new OffreDeTravail());
    }

    /**
     * @Route("offre_de_travail/{idOffreDeTravail}/modifier")
     */
    public function modifierOffreDeTravail(Request $request, $idOffreDeTravail)
    {
        return $this->manipulerOffreDeTravail($request, 'Modifier',
            $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail));
    }

    public function manipulerOffreDeTravail(Request $request, $manipulation, $offreDeTravail)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(OffreDeTravailType::class, $offreDeTravail)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $offreDeTravail = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($offreDeTravail);
                $entityManager->flush();

                return $this->redirect('/admin/offre_de_travail');
            }

            return $this->render('/admin/offre_de_travail/manipuler.html.twig', [
                'offreDeTravail' => $offreDeTravail,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("offre_de_travail/{idOffreDeTravail}/supprimer")
     */
    public function supprimerOffreDeTravail($idOffreDeTravail)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(OffreDeTravail::class)->find($idOffreDeTravail));
        $entityManager->flush();

        return $this->redirect('/admin/offre_de_travail');
    }
}
