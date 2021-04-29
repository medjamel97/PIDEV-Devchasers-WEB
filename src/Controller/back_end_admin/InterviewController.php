<?php

namespace App\Controller\back_end_admin;

use App\Entity\Interview;
use App\Entity\User;
use App\Form\InterviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class InterviewController extends AbstractController
{
    /**
     * @Route("interview")
     */
    public function afficherToutInterview()
    {
        return $this->render('admin/interview/afficher_tout.html.twig', [
            'interviews' => $this->getDoctrine()->getRepository(Interview::class)->findAll()
        ]);
    }

    /**
     * @Route("interview/ajouter")
     */
    public function ajouterInterview(Request $request)
    {
        return $this->manipulerInterview($request, 'Ajouter', new Interview());
    }

    /**
     * @Route("interview/{idInterview}/modifier")
     */
    public function modifierInterview(Request $request, $idInterview)
    {
        return $this->manipulerInterview($request, 'Modifier',
            $this->getDoctrine()->getRepository(Interview::class)->find($idInterview));
    }

    public function manipulerInterview(Request $request, $manipulation, $interview)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(InterviewType::class, $interview)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $interview = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($interview);
                $entityManager->flush();

                return $this->redirect('/admin/interview');
            }

            return $this->render('/admin/interview/manipuler.html.twig', [
                'interview' => $interview,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("interview/{idInterview}/supprimer")
     */
    public function supprimerInterview($idInterview)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Interview::class)->find($idInterview));
        $entityManager->flush();

        return $this->redirect('/admin/interview');
    }
}
