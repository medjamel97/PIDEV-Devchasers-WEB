<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionController extends AbstractController
{
    /**
     * @Route("/societe={idSociete}/mission={idMission}", name="afficherMission")
     */
    public function afficherMission($idSociete, $idMission): Response
    {
        return null;
    }

    /**
     * @Route("/mission", name="afficherToutMission")
     */
    public function afficherToutMission(): Response
    {
        return $this->render('/frontEnd/utilisateur/societe/mission/afficherMission.html.twig', [
            'missions' => $this->getDoctrine()->getManager()->getRepository(Mission::class)->findAll(),
        ]);
    }

    /**
     * @Route("/societe={idSociete}/mission/ajouter", name="ajouterMission")
     */
    public function ajouterMission(Request $request,$idSociete)
    {
        $mission = new Mission();

        $form = $this->createForm(MissionType::class, $mission)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mission = $form->getData();

            $missionRepository = $this->getDoctrine()->getManager();
            $missionRepository->persist($mission);
            $missionRepository->flush();

            return $this->redirectToRoute('afficherToutMission');
        }

        return $this->render('/frontEnd/utilisateur/societe/mission/manipulerMission.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission={idMission}/modifier", name="modifierMission")
     */
    public function modifierMission(Request $request, $idMission)
    {
        $missionRepository = $this->getDoctrine()->getManager();
        $mission = $missionRepository->getRepository(Mission::class)->find($idMission);

        $form = $this->createForm(MissionType::class, $mission);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $missionRepository->flush();
            return $this->redirectToRoute('afficherToutMission');
        }

        return $this->render('/frontEnd/utilisateur/societe/mission/manipulerMission.html.twig', [
            'form' => $form->createView(),
            'manipulation' => "Modifier",
        ]);
    }

    /**
     * @Route("/societe/mission={idMission}/supprimer", name="supprimerMission")
     */
    public function supprimerMission($idMission)
    {
        $missionManager = $this->getDoctrine()->getManager();
        $mission = $missionManager->getRepository(Mission::class)->find($idMission);
        $missionManager->remove($mission);
        $missionManager->flush();
        return $this->redirectToRoute('afficherToutMission');
    }
}
