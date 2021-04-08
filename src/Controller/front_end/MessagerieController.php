<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessagerieController extends AbstractController
{
    /**
     * @Route("messagerie", name="messagerie")
     */
    public function messagerie(Request $request)
    {
        return $this->render('front_end/candidat/messagerie/afficher.html.twig');
    }

    /**
     * @Route("recupererConversations", name="recupererConversations")
     */
    public function recupererConversations(Request $request)
    {
        $idCandidatExpediteur = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)])->getCandidat()->getId();

        $recherche = $request->get('recherche');

        $conversationsRepository = $this->getDoctrine()->getRepository(Conversation::class);
        $conversations = $conversationsRepository->findMyConversationSortedStartingWith($recherche, $idCandidatExpediteur);

        $i = 0;
        $jsonContent = null;
        if ($conversations != null) {
            foreach ($conversations as $conversation) {
                if ($conversation->getDateDernierMessage() != null) {
                    $jsonContent[$i]["idConversation"] = $conversation->getId();
                    $jsonContent[$i]["idPhoto"] = $conversation->getCandidatDestinataire()->getIdPhoto();
                    $jsonContent[$i]["nomConversation"] =
                        $conversation->getCandidatDestinataire()->getPrenom() . " " .
                        $conversation->getCandidatDestinataire()->getNom();
                    $jsonContent[$i]["dernierMessage"] = $conversationsRepository->findDernierMessage($conversation->getId());
                    $jsonContent[$i]["dernierMessageEstVu"] = $conversationsRepository->findDernierMessageEstVu($conversation->getId());
                    $jsonContent[$i]["nombreNotifications"] = $conversationsRepository->getNombreMessageNonLues($conversation->getId());
                    $i++;
                }

            }
            if ($jsonContent == null) {
                return new Response(null);
            } else {
                return new Response(json_encode($jsonContent));
            }

        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("ajouterConversation", name="ajouterConversation")
     * @throws ExceptionInterface
     */
    public
    function ajouterConversation(Request $request)
    {
        $param1 = (integer)$request->get('idCandidatExpediteur');
        $param2 = (integer)$request->get('idCandidatDestinataire');

        if (($param1 == null) || ($param2 == null)) {
            return null;
        }

        $candidatExpediteur = $this->getDoctrine()->getRepository(Candidat::class)->find($param1);
        $candidatDestinataire = $this->getDoctrine()->getRepository(Candidat::class)->find($param2);

        $conversation = $this->getDoctrine()->getRepository(Conversation::class)->findOneBy([
            'candidatExpediteur' => $candidatExpediteur,
            'candidatDestinataire' => $candidatDestinataire
        ]);

        if ($conversation == null) {
            $conversation = new Conversation();
            $conversationManager = $this->getDoctrine()->getManager();
            $conversation
                ->setCandidatExpediteur($candidatExpediteur)
                ->setCandidatDestinataire($candidatDestinataire);
            $conversationManager->persist($conversation);
            $conversationManager->flush();
        }

        $jsonContent['idConversation'] = $conversation->getId();
        $jsonContent['nomConversation'] = $candidatDestinataire->getNom() . " " . $candidatDestinataire->getPrenom();
        $jsonContent['idPhoto'] = $candidatDestinataire->getIdPhoto();
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("supprimerConversation", name="supprimerConversation")
     */
    public
    function supprimerConversation(Request $request)
    {
        $idConversation = (integer)$request->get('idConversation');

        $conversationManager = $this->getDoctrine()->getManager();
        $conversationManager->remove($conversationManager->getRepository(Conversation::class)->find($idConversation));
        $conversationManager->flush();

        return new Response("id = " . $idConversation . " Supprimé avec succes");
    }

    /**
     * @Route("recupererMessages", name="recupererMessages")
     * @throws ExceptionInterface
     */
    public
    function recupererMessages(Request $request)
    {
        $idConversation = (integer)$request->get('idConversation');
        $page = $request->get('page');

        $conversation = $this->getDoctrine()->getRepository(Conversation::class)->find($idConversation);
        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->findLastMessages($idConversation, 5, $page);

        // ---- FAIRE VU ----------------------------------------------------------------------------
        $manager = $this->getDoctrine()->getManager();
        $unseenMessages = $manager->getRepository(Message::class)
            ->findUnseenMessages($conversation);
        if ($unseenMessages != null) {
            foreach ($unseenMessages as $unseenMessage) {
                $unseenMessage->setEstVu(true);
                $manager->flush();
            }
        }
        // ------------------------------------------------------------------------------------------

        $jsonContent = null;
        $i = 0;
        foreach ($messages as $message) {
            $jsonContent[$i]['id'] = $message->getId();
            $jsonContent[$i]['contenu'] = $message->getContenu();
            $jsonContent[$i]['dateCreation'] = $message->getDateCreation()->format('d-m-Y h:i');
            $jsonContent[$i]['estProprietaire'] = $message->getEstProprietaire();
            $jsonContent[$i]['estVu'] = $message->getEstVu();
            $i++;
        }


        if ($jsonContent != null) {
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("nouveauMessage", name="nouveauMessage")
     * @throws Exception
     */
    public
    function nouveauMessage(Request $request)
    {
        $idConversation = (integer)$request->get('idConversation');
        $contenu = $request->get('contenu');
        $manager = $this->getDoctrine()->getManager();
        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));

        if (($idConversation == null) || ($contenu == null)) {
            return null;
        }

        $conversationExpediteur = $this->getDoctrine()->getRepository(Conversation::class)->find($idConversation);

        $conversationDestinataire = $manager->getRepository(Conversation::class)
            ->findOneBy([
                'candidatExpediteur' => $conversationExpediteur->getCandidatDestinataire(),
                'candidatDestinataire' => $conversationExpediteur->getCandidatExpediteur()
            ]);

        if ($conversationDestinataire == null) {
            $conversationDestinataire = new Conversation();
            $conversationDestinataire
                ->setCandidatExpediteur($conversationExpediteur->getCandidatDestinataire())
                ->setCandidatDestinataire($conversationExpediteur->getCandidatExpediteur());
            $manager->persist($conversationDestinataire);
        }

        $this->createMessage($manager, $contenu, $date, $conversationExpediteur, true);
        $this->createMessage($manager, $contenu, $date, $conversationDestinataire, false);

        $manager->flush();

        return new Response(null);
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

        if ($proprietaire) {
            $message->setEstVu(true);
        }

        $manager->persist($message);

        $conversation->setDateDernierMessage($date);
    }
}