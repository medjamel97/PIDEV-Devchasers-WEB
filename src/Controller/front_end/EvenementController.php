<?php

namespace App\Controller\front_end;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    /**
     * @Route("evenement", name="calendrier_evenement")
     */
    public function calendrierEvenement()
    {
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $rendezVous = [];
        foreach ($evenements as $evenement) {
            $rendezVous[] = [
                'id' => $evenement->getId(),
                'societe' => $evenement->getSociete()->getId(),
                'start' => $evenement->getDebut()->format('Y-m-d H:i:s'),
                'end' => $evenement->getFin()->format('Y-m-d H:i:s'),
                'title' => $evenement->getTitre(),
                'description' => $evenement->getDescription(),
                'allDay' => $evenement->getAllDay(),

            ];
        }

        $data = json_encode($rendezVous);

        return $this->render('front_end/societe/evenement/calendrier.html.twig', compact('data'));
    }

    /**
     * @Route("evenement/{idEvenement}/afficher", name="afficher_evenement")
     */
    public function afficherEvenement($idEvenement)
    {
        return $this->render('front_end/societe/evenement/afficher.html.twig', [
            'evenement' => $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement),
        ]);
    }
}