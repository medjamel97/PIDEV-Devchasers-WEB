<?php

namespace App\Controller\back_end_admin;

use App\Entity\Evenement;
use App\Entity\User;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class EvenementController extends AbstractController
{
    /**
     * @Route("evenement")
     */
    public function afficherToutEvenement()
    {
        return $this->render('admin/evenement/afficher_tout.html.twig', [
            'evenements' => $this->getDoctrine()->getRepository(Evenement::class)->findAll()
        ]);
    }

    /**
     * @Route("evenement/ajouter")
     */
    public function ajouterEvenement(Request $request)
    {
        return $this->manipulerEvenement($request, 'Ajouter', new Evenement());
    }

    /**
     * @Route("evenement/{idEvenement}/modifier")
     */
    public function modifierEvenement(Request $request, $idEvenement)
    {
        return $this->manipulerEvenement($request, 'Modifier',
            $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement));
    }

    public function manipulerEvenement(Request $request, $manipulation, $evenement)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(EvenementType::class, $evenement)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $evenement = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($evenement);
                $entityManager->flush();

                return $this->redirect('/admin/evenement');
            }

            return $this->render('/admin/evenement/manipuler.html.twig', [
                'evenement' => $evenement,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("evenement/{idEvenement}/supprimer")
     */
    public function supprimerEvenement($idEvenement)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement));
        $entityManager->flush();

        return $this->redirect('/admin/evenement');
    }
}
