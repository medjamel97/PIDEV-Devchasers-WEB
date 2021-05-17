<?php

namespace App\Controller\back_end_admin;

use App\Entity\Candidat;
use App\Entity\User;
use App\Form\CandidatType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_admin/")
 */
class CandidatController extends AbstractController
{
    /**
     * @Route("candidat")
     */
    public function afficherToutCandidat()
    {
        return $this->render('/back_end_admin/candidat/afficher_tout.html.twig', [
            'candidats' => $this->getDoctrine()->getRepository(Candidat::class)->findAll()
        ]);
    }

    /**
     * @Route("candidat/ajouter")
     */
    public function ajouterCandidat(Request $request)
    {
        return $this->manipulerCandidat($request, 'Ajouter', new Candidat());
    }

    /**
     * @Route("candidat/{idCandidat}/modifier")
     */
    public function modifierCandidat(Request $request, $idCandidat)
    {
        return $this->manipulerCandidat($request, 'Modifier',
            $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat));
    }

    public function manipulerCandidat(Request $request, $manipulation, $candidat)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(CandidatType::class, $candidat)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $candidat = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($candidat);
                $entityManager->flush();

                return $this->redirect('/espace_admin/candidat');
            }

            return $this->render('/back_end_admin/candidat/manipuler.html.twig', [
                'candidat' => $candidat,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("candidat/{idCandidat}/supprimer")
     */
    public function supprimerCandidat($idCandidat)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat));
        $entityManager->flush();

        return $this->redirect('/espace_admin/candidat');
    }
}
