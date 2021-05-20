<?php

namespace App\Controller\Mobile;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class MessagerieController extends AbstractController
{
    /**
     * @Route("recuperer_contacts")
     */
    public function recupererContacts(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        if ($users == null) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($users as $user) {
            $jsonContent[$i]['userId'] = $user->getId();
            if ($user->getCandidat()) {
                $jsonContent[$i]['role'] = "ROLE_CANDIDAT";
                $jsonContent[$i]['nomComplet'] = $user->getCandidat()->getPrenom() . " " . $user->getCandidat()->getNom();
                $jsonContent[$i]['idPhoto'] = $user->getCandidat()->getIdPhoto();
            } elseif ($user->getSociete()) {
                $jsonContent[$i]['role'] = "ROLE_SOCIETE";
                $jsonContent[$i]['nomComplet'] = $user->getSociete()->getNom();
                $jsonContent[$i]['idPhoto'] = $user->getSociete()->getIdPhoto();
            } else {
                $jsonContent[$i]['role'] = "ROLE_ADMIN";
                $jsonContent[$i]['nomComplet'] = "Admin";
                $jsonContent[$i]['idPhoto'] = "null";
            }
            $i++;
        }
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("recuperer_conversations")
     */
    public function recupererConversations(Request $request): Response
    {
        $idUserExpediteur = $this->getDoctrine()->getRepository(User::class)->find($request->get("userId"));

        $recherche = $request->get('recherche');

        $conversationsRepository = $this->getDoctrine()->getRepository(Conversation::class);
        $conversations = $conversationsRepository->findMyConversationSortedStartingWith($recherche, $idUserExpediteur);

        $i = 0;
        $jsonContent = null;
        if ($conversations != null) {
            foreach ($conversations as $conversation) {
                if ($conversation->getDateDernierMessage() != null) {
                    $jsonContent[$i]["id"] = $conversation->getId();
                    $jsonContent[$i]["nom"] = $conversation->getNom();
                    foreach ($conversation->getUserDestinataire()->getRoles() as $role) {
                        if ($role == "ROLE_ADMIN") {
                            $jsonContent[$i]["idPhoto"] = "/images/admin-icon.png";
                        } elseif ($role == "ROLE_SOCIETE") {
                            $jsonContent[$i]["idPhoto"] = $conversation->getUserDestinataire()->getSociete()->getIdPhoto();
                        } elseif ($role == "ROLE_CANDIDAT") {
                            $jsonContent[$i]["idPhoto"] = $conversation->getUserDestinataire()->getCandidat()->getIdPhoto();
                        }
                    }
                    $dernierMessage = $conversationsRepository->findDernierMessage($conversation->getId());
                    if ($dernierMessage['estProprietaire']) {
                        $jsonContent[$i]["dernierMessage"] = 'Vous : ' . $dernierMessage['contenu'];
                    } else {
                        $jsonContent[$i]["dernierMessage"] = $dernierMessage['contenu'];
                    }
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
     * @Route("ajouter_conversation")
     */
    public function ajouterConversation(Request $request): Response
    {
        $param1 = (integer)$request->get('idUserExpediteur');
        $param2 = (integer)$request->get('idUserDestinataire');

        if (($param1 == null) || ($param2 == null)) {
            return new Response(null);
        }

        $userExpediteur = $this->getDoctrine()->getRepository(User::class)->find($param1);
        $userDestinataire = $this->getDoctrine()->getRepository(User::class)->find($param2);

        $conversation = $this->getDoctrine()->getRepository(Conversation::class)->findOneBy([
            'userExpediteur' => $userExpediteur,
            'userDestinataire' => $userDestinataire
        ]);
        $jsonContent = null;
        if ($conversation == null) {
            $conversation = new Conversation();
            $conversationManager = $this->getDoctrine()->getManager();
            $conversation
                ->setUserExpediteur($userExpediteur)
                ->setUserDestinataire($userDestinataire)
                ->setNom("null");

            foreach ($userDestinataire->getRoles() as $role) {
                if ($role == "ROLE_ADMIN") {
                    $conversation->setNom("Admin");
                } elseif ($role == "ROLE_SOCIETE") {
                    $conversation->setNom($userDestinataire->getSociete()->getNom());
                } elseif ($role == "ROLE_CANDIDAT") {
                    $conversation->setNom(
                        $userDestinataire->getCandidat()->getPrenom()
                        . " " . $userDestinataire->getCandidat()->getNom()
                    );
                }
            }

            $conversationManager->persist($conversation);
            $conversationManager->flush();
        }

        $conversationsRepository = $this->getDoctrine()->getRepository(Conversation::class);

        $jsonContent[0]['id'] = $conversation->getId();
        $jsonContent[0]['nom'] = $conversation->getNom();
        $jsonContent[0]['dernierMessageEstVu'] = $conversationsRepository->findDernierMessageEstVu($conversation->getId());
        $jsonContent[0]['nombreNotifications'] = $conversationsRepository->getNombreMessageNonLues($conversation->getId());

        foreach ($userDestinataire->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                $jsonContent[0]['idPhoto'] = "/images/admin-icon.png";
            } elseif ($role == "ROLE_SOCIETE") {
                $jsonContent[0]['idPhoto'] = $conversation->getUserDestinataire()->getSociete()->getIdPhoto();
            } elseif ($role == "ROLE_CANDIDAT") {
                $jsonContent[0]['idPhoto'] = $conversation->getUserDestinataire()->getCandidat()->getIdPhoto();
            }
        }

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("supprimer_conversation")
     */
    public function supprimerConversation(Request $request): Response
    {
        $idConversation = (integer)$request->get('id');

        $conversationManager = $this->getDoctrine()->getManager();
        $conversationManager->remove($conversationManager->getRepository(Conversation::class)->find($idConversation));
        $conversationManager->flush();

        return new Response("id = " . $idConversation . " Supprimé avec succes");
    }

    /**
     * @Route("recuperer_messages")
     */
    public function recupererMessages(Request $request): Response
    {
        $idConversation = (int)$request->get('id');
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
            $jsonContent[$i]['dateCreation'] = $message->getDateCreation()->format('H:i - d/M/Y');
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
     * @Route("nouveau_message")
     * @throws Exception
     */
    public function nouveauMessage(Request $request): Response
    {
        $idConversation = (integer)$request->get('id');
        $contenu = $request->get('contenu');
        $manager = $this->getDoctrine()->getManager();

        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));

        if (($idConversation == null) || ($contenu == null)) {
            return new Response(null);
        }

        $conversationExpediteur = $this->getDoctrine()->getRepository(Conversation::class)->find($idConversation);

        $conversationDestinataire = $manager->getRepository(Conversation::class)
            ->findOneBy([
                'userExpediteur' => $conversationExpediteur->getUserDestinataire(),
                'userDestinataire' => $conversationExpediteur->getUserExpediteur()
            ]);

        if ($conversationDestinataire == null) {
            $conversationDestinataire = new Conversation();
            $conversationDestinataire
                ->setUserExpediteur($conversationExpediteur->getUserDestinataire())
                ->setUserDestinataire($conversationExpediteur->getUserExpediteur());
            foreach ($conversationDestinataire->getUserDestinataire()->getRoles() as $role) {
                if ($role == "ROLE_ADMIN") {
                    $conversationDestinataire->setNom("Admin");
                } elseif ($role == "ROLE_SOCIETE") {
                    $conversationDestinataire->setNom($conversationDestinataire->getUserDestinataire()->getSociete()->getNom());
                } elseif ($role == "ROLE_CANDIDAT") {
                    $conversationDestinataire->setNom(
                        $conversationDestinataire->getUserDestinataire()->getCandidat()->getPrenom()
                        . " " . $conversationDestinataire->getUserDestinataire()->getCandidat()->getNom()
                    );
                }
            }
            $manager->persist($conversationDestinataire);
        }

        $this->createMessage($manager, $contenu, $date, $conversationExpediteur, true);
        $this->createMessage($manager, $contenu, $date, $conversationDestinataire, false);

        $manager->flush();

        return new Response("Ajout effectué");
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
