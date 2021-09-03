<?php

namespace App\Controller\Mobile;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("mobile/")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("recuperer_users")
     * @return Response
     */
    public function recupererUsers(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $jsonContent = null;
        $i = 0;
        foreach ($users as $user) {
            $jsonContent[$i]['id'] = $user->getId();
            if ($user->getCandidat()) {
                $jsonContent[$i]['candidatId'] = $user->getCandidat()->getId();
            } else {
                $jsonContent[$i]['candidatId'] = 0;
            }
            if ($user->getSociete()) {
                $jsonContent[$i]['societeId'] = $user->getSociete()->getId();
            } else {
                $jsonContent[$i]['societeId'] = 0;
            }
            $jsonContent[$i]['email'] = $user->getEmail();
            $jsonContent[$i]['roles'] = $user->getRoles();
            $jsonContent[$i]['password'] = $user->getPassword();
            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("recuperer_user_par_email")
     * @return Response
     */
    public function recupererUser(Request $request): Response
    {
        $email = $request->get("email");

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        $jsonContent[0]['id'] = $user->getId();
        $jsonContent[0]['candidatId'] = $user->getId();
        $jsonContent[0]['societeId'] = $user->getId();
        $jsonContent[0]['email'] = $user->getEmail();
        $jsonContent[0]['roles'] = $user->getRoles();
        $jsonContent[0]['password'] = $user->getPassword();

        return new Response(json_encode($jsonContent));
    }


    /**
     * @Route("verication_mot_de_passe")
     * @return Response
     */
    public function verifierUser(Request $request): Response
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if ($user) {
            if ($this->passwordEncoder->isPasswordValid($user, $password)) {
                return new Response(json_encode(['isValid' => true]));
            } else {
                return new Response(json_encode(['isValid' => false]));
            }
        } else {
            return new Response(json_encode(['isValid' => false]));
        }
    }
}
