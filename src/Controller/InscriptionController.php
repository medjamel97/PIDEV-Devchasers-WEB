<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Societe;
use App\Entity\User;
use App\Form\CandidatType;
use App\Form\RegistrationFormType;
use App\Form\SocieteType;
use App\Security\EmailVerifier;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class InscriptionController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("inscription/candidat", name="inscription_candidat")
     * @throws Exception
     */
    public function inscriptionCandidat(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getSession()->get(Security::LAST_USERNAME)){
            return $this->redirect("/accueil");
        }

        $user = new User();
        $candidat = new Candidat();

        $form = $this->createForm(RegistrationFormType::class, $user)
            ->add("candidat", CandidatType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setCandidat($candidat)->setRoles(["ROLE_CANDIDAT"])
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ));

            $file = $request->files->get('registration_form')['candidat']['idPhoto'];
            $uploads_directory = $this->getParameter('uploads_directory_candidat');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );

            $dateNaissance = new \DateTime(
                $request->get('registration_form')['candidat']['dateNaissance']['day'] . "-" .
                $request->get('registration_form')['candidat']['dateNaissance']['month'] . "-" .
                $request->get('registration_form')['candidat']['dateNaissance']['year']
            );

            $candidat
                ->setUser($user)
                ->setNom($request->get('registration_form')['candidat']['nom'])
                ->setPrenom($request->get('registration_form')['candidat']['prenom'])
                ->setTel($request->get('registration_form')['candidat']['tel'])
                ->setSexe($request->get('registration_form')['candidat']['sexe'])
                ->setDateNaissance($dateNaissance)
                ->setIdPhoto('http://localhost/khedemti/images/candidat/' . $filename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidat);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('jamelbd97@gmail.com', 'Jamel'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('_inscription/_confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('connexion');
        }

        $this->addFlash('success', 'Veuillez verifier votre email.');

        return $this->render('_inscription/candidat.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("inscription/societe", name="inscription_societe")
     * @throws Exception
     */
    public function inscriptionSociete(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getSession()->get(Security::LAST_USERNAME)){
            return $this->redirect("/accueil");
        }

        $user = new User();
        $societe = new Societe();

        $form = $this->createForm(RegistrationFormType::class, $user)
            ->add("societe", SocieteType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setSociete($societe)->setRoles(["ROLE_SOCIETE"])
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ));

            $file = $request->files->get('registration_form')['societe']['idPhoto'];
            $uploads_directory = $this->getParameter('uploads_directory_societe');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );

            $dateCreation = new \DateTime(
                $request->get('registration_form')['societe']['dateCreation']['day'] . "-" .
                $request->get('registration_form')['societe']['dateCreation']['month'] . "-" .
                $request->get('registration_form')['societe']['dateCreation']['year']
            );

            $societe
                ->setUser($user)
                ->setNom($request->get('registration_form')['societe']['nom'])
                ->setTel($request->get('registration_form')['societe']['tel'])
                ->setDateCreation($dateCreation)
                ->setIdPhoto('http://localhost/khedemti/images/societe/' . $filename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($societe);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('jamelbd97@gmail.com', 'Jamel'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('_inscription/_confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('connexion');
        }

        $this->addFlash('success', 'Veuillez verifier votre email.');

        return $this->render('_inscription/societe.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('connexion');
    }
}
