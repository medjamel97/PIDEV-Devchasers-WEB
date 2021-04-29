<?php

namespace App\Controller\back_end_admin;

use App\Entity\Mission;
use App\Entity\User;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("admin/")
 */
class MissionController extends AbstractController
{
    /**
     * @Route("mission")
     */
    public function afficherToutMission()
    {
        return $this->render('admin/mission/afficher_tout.html.twig', [
            'missions' => $this->getDoctrine()->getRepository(Mission::class)->findAll()
        ]);
    }

    /**
     * @Route("mission/ajouter")
     */
    public function ajouterMission(Request $request)
    {
        return $this->manipulerMission($request, 'Ajouter', new Mission());
    }

    /**
     * @Route("mission/{idMission}/modifier")
     */
    public function modifierMission(Request $request, $idMission)
    {
        return $this->manipulerMission($request, 'Modifier',
            $this->getDoctrine()->getRepository(Mission::class)->find($idMission));
    }

    public function manipulerMission(Request $request, $manipulation, $mission)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(MissionType::class, $mission)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $mission = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($mission);
                $entityManager->flush();

                return $this->redirect('/admin/mission');
            }

            return $this->render('/admin/mission/manipuler.html.twig', [
                'mission' => $mission,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("mission/{idMission}/supprimer")
     */
    public function supprimerMission($idMission)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($this->getDoctrine()->getRepository(Mission::class)->find($idMission));
        $entityManager->flush();

        return $this->redirect('/admin/mission');
    }
}
