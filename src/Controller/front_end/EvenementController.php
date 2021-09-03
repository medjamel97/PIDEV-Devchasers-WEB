<?php

namespace App\Controller\front_end;

use App\Entity\Evenement;
use App\Entity\Societe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    /**
     * @Route("evenement")
     */
    public function evenement(): Response
    {
        return $this->render('front_end/societe/evenement/afficher_tout.html.twig', [
            'evenements' => $this->getDoctrine()->getRepository(Evenement::class)->findBy([], [
                'debut' => 'ASC'
            ])
        ]);
    }

    /**
     * @Route("evenement/societe={societeId}")
     */
    public function evenementParSociete($societeId): Response
    {
        $societe = $this->getDoctrine()->getRepository(Societe::class)->find($societeId);
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy([
            'societe' => $societe
        ], [
            'debut' => 'ASC'
        ]);

        return $this->render('front_end/societe/evenement/afficher_par_societe.html.twig', [
            'societe' => $societe,
            'evenements' => $evenements
        ]);
    }

    /**
     * @Route("calendrier/societe={societeId}", name="calendrier_evenement")
     */
    public function calendrierEvenement($societeId): Response
    {
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy([
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($societeId)
        ]);

        $rendezVous = [];
        foreach ($evenements as $evenement) {
            $rendezVous[] = [
                'id' => $evenement->getId(),
                'societe' => $evenement->getSociete()->getId(),
                'title' => $evenement->getTitre(),
                'start' => $evenement->getDebut()->format('Y-m-d H:i'),
                'end' => $evenement->getFin()->format('Y-m-d H:i'),
                'description' => $evenement->getDescription(),
                'allDay' => $evenement->getAllDay(),
            ];
        }
        $data = json_encode($rendezVous);
        return $this->render('front_end/societe/evenement/calendrier.html.twig', compact('data'));
    }
}
