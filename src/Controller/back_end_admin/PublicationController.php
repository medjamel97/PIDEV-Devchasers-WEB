<?php

namespace App\Controller\back_end_admin;

use App\Entity\Publication;
use App\Entity\User;
use App\Form\PublicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_admin/")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("publication")
     */
    public function afficherToutPublication(): Response
    {
        return $this->render('/back_end_admin/publication/afficher_tout.html.twig', [
            'publications' => $this->getDoctrine()->getRepository(Publication::class)->findAll()
        ]);
    }

    /**
     * @Route("publication/ajouter")
     */
    public function ajouterPublication(Request $request): Response
    {
        return $this->manipulerPublication($request, 'Ajouter', new Publication());
    }

    /**
     * @Route("publication/{idPublication}/modifier")
     */
    public function modifierPublication(Request $request, $idPublication): Response
    {
        return $this->manipulerPublication($request, 'Modifier',
            $this->getDoctrine()->getRepository(Publication::class)->find($idPublication));
    }

    public function manipulerPublication(Request $request, $manipulation, $publication)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(PublicationType::class, $publication)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $publication = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($publication);
                $entityManager->flush();

                return $this->redirect('/espace_admin/publication');
            }

            return $this->render('/back_end_admin/publication/manipuler.html.twig', [
                'publication' => $publication,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("publication/{idPublication}/supprimer")
     */
    public function supprimerPublication($idPublication): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Publication::class)->find($idPublication));
        $entityManager->flush();

        return $this->redirect('/espace_admin/publication');
    }
}
