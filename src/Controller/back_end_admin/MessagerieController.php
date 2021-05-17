<?php

namespace App\Controller\back_end_admin;

use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_admin/messagerie")
 */
class MessagerieController extends AbstractController
{
    /**
     * @Route("",defaults={"idUserDestinataire"=null})
     * @Route("/{idUserDestinataire}/afficher")
     */
    public function messagerie(Request $request, $idUserDestinataire): Response
    {
        $idConversation = null;
        if ($idUserDestinataire) {
            $idUserExpediteur = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
                $request->getSession()->get(Security::LAST_USERNAME)])->getId();

            $request->attributes->set('idUserExpediteur', $idUserExpediteur);
            $request->attributes->set('idUserDestinataire', $idUserDestinataire);
            $idConversation = json_decode($this->ajouterConversation($request)->getContent(),true)['id'];
        }
        return $this->render('/back_end_admin/messagerie/afficher.html.twig', [
            'conversation' =>
                $idConversation ? $this->getDoctrine()->getRepository(Conversation::class)->find($idConversation) : null
        ]);
    }

    /**
     * @Route("/recuperer_users", name="recuperer_users")
     */
    public function recupererUsers(Request $request): Response
    {
        $idUserExpediteur = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)])->getId();

        $recherche = $request->get('recherche');

        $users = $this->getDoctrine()->getRepository(User::class)->findStartingWith($recherche);

        $i = 0;
        $jsonContent = null;
        if ($users != null) {
            foreach ($users as $userDestinataire) {
                if ($userDestinataire->getId() != $idUserExpediteur) {
                    $jsonContent[$i]["idUserExpediteur"] = $idUserExpediteur;
                    $jsonContent[$i]["idUserDestinataire"] = $userDestinataire->getId();
                    foreach ($userDestinataire->getRoles() as $role) {
                        if ($role == "ROLE_ADMIN") {
                            $jsonContent[$i]["idPhoto"] = "/images/admin-icon.png";
                            $jsonContent[$i]["nom"] = "Admin";
                        } elseif ($role == "ROLE_SOCIETE") {
                            $jsonContent[$i]["idPhoto"] = $userDestinataire->getSociete()->getIdPhoto();
                            $jsonContent[$i]["nom"] =
                                "Societe : " . $userDestinataire->getSociete()->getNom();

                        } elseif ($role == "ROLE_CANDIDAT") {
                            $jsonContent[$i]["idPhoto"] = $userDestinataire->getCandidat()->getIdPhoto();
                            $jsonContent[$i]["nom"] =
                                $userDestinataire->getCandidat()->getPrenom() . " " .
                                $userDestinataire->getCandidat()->getNom();
                        }
                    }
                }
                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("/recuperer_conversations")
     */
    public function recupererConversations(Request $request): Response
    {
        $idUserExpediteur = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)])->getId();

        $recherche = $request->get('recherche');

        $conversationsRepository = $this->getDoctrine()->getRepository(Conversation::class);
        $conversations = $conversationsRepository->findMyConversationSortedStartingWith($recherche, $idUserExpediteur);

        $i = 0;
        $jsonContent = null;
        $conversation = new Conversation();
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
                    }                    $jsonContent[$i]["dernierMessageEstVu"] = $conversationsRepository->findDernierMessageEstVu($conversation->getId());
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
     * @Route("/ajouter_conversation")
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

        $jsonContent['id'] = $conversation->getId();
        $jsonContent['nom'] = $conversation->getNom();

        foreach ($userDestinataire->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                $jsonContent['idPhoto'] = "/images/admin-icon.png";
            } elseif ($role == "ROLE_SOCIETE") {
                $jsonContent['idPhoto'] = $conversation->getUserDestinataire()->getSociete()->getIdPhoto();
            } elseif ($role == "ROLE_CANDIDAT") {
                $jsonContent['idPhoto'] = $conversation->getUserDestinataire()->getCandidat()->getIdPhoto();
            }
        }

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/supprimer_conversation")
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
     * @Route("/recuperer_messages")
     */
    public function recupererMessages(Request $request): Response
    {
        $idConversation = (integer)$request->get('id');
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
     * @Route("/nouveau_message")
     * @throws Exception
     */
    public function nouveauMessage(Request $request): Response
    {
        $idConversation = (integer)$request->get('id');
        $contenu = $request->get('contenu');
        $manager = $this->getDoctrine()->getManager();
        $date = new DateTime('now', new DateTimeZone('Africa/Tunis'));

        if (($idConversation == null) || ($contenu == null)) {
            throw new Exception("id ou contenu null");
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
