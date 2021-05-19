<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\Categorie;
use App\Entity\Societe;
use App\Entity\User;
use App\Form\CandidatType;
use App\Form\RegistrationFormType;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("mobile/")
 */
class InscriptionController extends AbstractController
{
    /**
     * @Route("inscription_candidat")
     * @throws Exception
     */
    public function inscriptionCandidat(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $candidat = new Candidat();

        // encode the plain password
        $user
            ->setCandidat($candidat)
            ->setRoles(["ROLE_CANDIDAT"])
            ->setEmail($request->get('email'))
            ->setIsSetUp(true)
            ->setIsVerified(true)
            ->setPassword($passwordEncoder->encodePassword(
                $user,
                $request->get('password')
            ));

        $dateNaissance =
            DateTime::createFromFormat('d/m/Y',
                (string)$request->get('day') . "/" .
                (string)$request->get('month') . "/" .
                (string)$request->get('year')
            );

        $candidat
            ->setUser($user)
            ->setNom($request->get('nom'))
            ->setPrenom($request->get('prenom'))
            ->setTel($request->get('tel'))
            ->setSexe($request->get('sexe'))
            ->setDateNaissance($dateNaissance);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($candidat);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("inscription effectué");
    }

    /**
     * @Route("inscription_societe")
     * @throws Exception
     */
    public function inscriptionSociete(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $societe = new societe();

        // encode the plain password
        $user
            ->setsociete($societe)
            ->setRoles(["ROLE_SOCIETE"])
            ->setEmail($request->get('email'))
            ->setIsSetUp(true)
            ->setIsVerified(true)
            ->setPassword($passwordEncoder->encodePassword(
                $user,
                $request->get('password')
            ));

        $dateCreation =
            DateTime::createFromFormat('d/m/Y',
                (string)$request->get('day') . "/" .
                (string)$request->get('month') . "/" .
                (string)$request->get('year')
            );

        $societe
            ->setUser($user)
            ->setNom($request->get('nom'))
            ->setTel($request->get('tel'))
            ->setDateCreation($dateCreation)
            ->setIdPhoto($request->get('idPhoto'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($societe);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("inscription effectué");
    }
}
