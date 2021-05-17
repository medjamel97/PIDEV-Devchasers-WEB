<?php

namespace App\Controller\back_end_societe;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_societe/")
 */
class ConversationController extends AbstractController
{
    public function afficherToutConversation(Request $request)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        return $this->render('back_end_societe/messagerie/conversations.html.twig', [
            'conversations' => $this->getDoctrine()->getRepository(Conversation::class)
                ->findBy([
                    'userExpediteur' => $user
                ], [
                    'dateDernierMessage' => 'DESC'
                ]),
        ]);
    }
}
