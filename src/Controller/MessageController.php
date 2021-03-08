<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/messagerie", name="afficherMessagerie")
     */
    public function afficherMessagerie(): Response
    {
        return $this->render('/frontEnd/utilisateur/candidat/messagerie/afficherMessagerie.html.twig', [
            'candidats' => $this->getDoctrine()->getRepository(Candidat::class)->findAll(),
        ]);
    }

    /**
     * @Route("/messagerie/candidat={idCandidat}/candidatExpediteur={idCandidatExpediteur}/candidatDestinataire={idCandidatDestinataire}", name="afficherMessage")
     */
    public function afficherMessage($idCandidat,$idCandidatExpediteur,$idCandidatDestinataire): Response
    {
        return $this->render('/frontEnd/utilisateur/candidat/messagerie/afficherMessage.html.twig', [
            'messages' => $this->getDoctrine()->getRepository(Message::class)
                ->findSinglePageResults(0,5,$idCandidatExpediteur,$idCandidatDestinataire),
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat),
        ]);
    }
}