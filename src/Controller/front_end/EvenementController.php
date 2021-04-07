<?php

namespace App\Controller\front_end;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class EvenementController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("evenement", name="calendrier_evenement")
     */
    public function calendrierEvenement()
    {
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $rdvs = [];
        foreach ($evenements as $evenement) {
            $rdvs[] = [
                'id' => $evenement->getId(),
                'id_user' => $evenement->getIdUser(),
                'start' => $evenement->getDebut()->format('Y-m-d H:i:s'),
                'end' => $evenement->getFin()->format('Y-m-d H:i:s'),
                'title' => $evenement->getTitre(),
                'descp' => $evenement->getDescription(),
                'allDay' => $evenement->getAllDay(),

            ];
        }

        $data = json_encode($rdvs);

        return $this->render('front_end/societe/evenement/calendrier.html.twig', compact('data'));
    }

    /**
     * @Route("evenement/{idEvenement}", name="afficher_evenement")
     */
    public function afficherEvenement($idEvenement)
    {
        return $this->render('front_end/societe/evenement/afficher.html.twig', [
            'evenement' => $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement),
        ]);
    }
}