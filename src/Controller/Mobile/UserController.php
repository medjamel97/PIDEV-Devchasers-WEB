<?php

namespace App\Controller\Mobile;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("recuperer_users")
     * @return Response
     */
    public function recupererUsers()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $jsonContent = null;
        $i = 0;
        $user = new User();
        foreach ($users as $user) {
            $jsonContent[$i]['id'] = $user->getId();
            $jsonContent[$i]['candidatId'] = $user->getId();
            $jsonContent[$i]['societeId'] = $user->getId();
            $jsonContent[$i]['email'] = $user->getEmail();
            $jsonContent[$i]['roles'] = $user->getRoles();
            $jsonContent[$i]['password'] = $user->getPassword();
            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }
}
