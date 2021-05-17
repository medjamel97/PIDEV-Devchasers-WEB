<?php

namespace App\Controller\back_end_admin;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_admin/")
 */
class ConversationController extends AbstractController
{
    /**
     * @Route("conversation")
     */
    public function afficherToutConversation(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        return $this->render('/back_end_admin/messagerie/conversations.html.twig', [
            'conversations' => $this->getDoctrine()->getRepository(Conversation::class)
                ->findBy([
                    'userExpediteur' => $user
                ],[
                    'dateDernierMessage' => 'DESC'
                ]),
        ]);
    }
}
