<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Conversation;
use App\Entity\Message;
use Cassandra\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessagerieController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("afficherMessagerie", name="afficherMessagerie")
     */
    public function afficherMessagerie(Request $request)
    {
        $idCandidatExpediteur = $this->session->get("utilisateur")['idCandidat'];

        $conversations = $this->getDoctrine()->getRepository(Conversation::class)
            ->findMyConversations($idCandidatExpediteur);

        return $this->render('/frontEnd/utilisateur/candidat/messagerie/afficherMessagerie.html.twig', [
            'candidats' => $this->getDoctrine()->getRepository(Candidat::class)->findAll(),
            'conversations' => $conversations,
        ]);
    }

    /**
     * @Route("afficherConversation", name="afficherConversation")
     * @throws ExceptionInterface
     */
    public function afficherConversation(Request $request, NormalizerInterface $normalizer)
    {
        $param1 = (integer)$request->get('idCandidatExpediteur');
        $param2 = (integer)$request->get('idCandidatDestinataire');

        $conversation = $this->getDoctrine()->getRepository(Conversation::class)
            ->findOneBy([
                'candidatExpediteur' => $param1,
                'candidatDestinataire' => $param2
            ]);
        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findLastMessages($conversation, 7);

        $jsonContent = $normalizer->normalize($messages, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    /**
     * @Route("supprimerConversation", name="supprimerConversation")
     * @throws ExceptionInterface
     */
    public function supprimerConversation(Request $request)
    {
        $idConversation = (integer)$request->get('idConversation');
        /*
                $conversationManager = $this->getDoctrine()->getManager();
                $conversationManager->remove($conversationManager->getRepository(Conversation::class)->find($idConversation));
                $conversationManager->flush();
        */

        return new Response("id = " . $idConversation . " Supprimé avec succes (controller not working)");
    }

    /**
     * @Route("nouveauMessage", name="nouveauMessage")
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function nouveauMessage(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $param1 = (integer)$request->get('idCandidatExpediteur');
        $candidatExpediteur = $this->getDoctrine()->getRepository(Candidat::class)->find($param1);
        $param2 = (integer)$request->get('idCandidatDestinataire');
        $candidatDestinataire = $this->getDoctrine()->getRepository(Candidat::class)->find($param2);
        $contenu = $request->get('contenu');
        $date = new \DateTime('now', new \DateTimeZone('Africa/Tunis'));

        $conversation1 = $this->getConversation($manager, $candidatExpediteur, $candidatDestinataire);
        $conversation2 = $this->getConversation($manager, $candidatDestinataire, $candidatExpediteur);

        $this->createMessage($manager, $contenu, $date, $conversation1, true);
        $this->createMessage($manager, $contenu, $date, $conversation2, false);

        $manager->flush();

        return new Response(null);
    }

    public function getConversation($manager, $candidatExpediteur, $candidatDestinataire)
    {
        $conversationExpediteur = $manager->getRepository(Conversation::class)
            ->findOneBy([
                'candidatExpediteur' => $candidatExpediteur,
                'candidatDestinataire' => $candidatDestinataire
            ]);

        if ($conversationExpediteur == null) {
            $conversationExpediteur = new Conversation();
            $conversationExpediteur->setCandidatExpediteur($candidatExpediteur)->setCandidatDestinataire($candidatDestinataire);
            $manager->persist($conversationExpediteur);
        }

        return $conversationExpediteur;
    }

    function createMessage($manager, $contenu, $date, $conversation, $proprietaire)
    {
        $message = new Message();
        $message
            ->setConversation($conversation)
            ->setContenu($contenu)
            ->setDateCreation($date)
            ->setEstProprietaire($proprietaire)
            ->setEstVu(false);

        $manager->persist($message);

        $conversation->setDateDernierMessage($date);
    }
}