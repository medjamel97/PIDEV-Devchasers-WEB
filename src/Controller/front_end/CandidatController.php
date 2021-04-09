<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use App\Entity\User;
use App\Form\CandidatType;
use App\Form\UserType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CandidatController extends AbstractController
{
    /**
     * @Route("candidat/{idCandidat}/profil", name="profil_candidat")
     */
    public function profile($idCandidat)
    {
        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($idCandidat);
        $educations = $this->getDoctrine()->getRepository(Education::class)->findOneBySomeField($idCandidat);
        $experienceDeTravails = $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->findOneBySomeField($idCandidat);
        $competences = $this->getDoctrine()->getRepository(Competence::class)->findOneBySomeField($idCandidat);

        return $this->render('front_end/candidat/_profile/afficher.html.twig', [
            'candidat' => $candidat,
            'educations' => $educations,
            'experienceDeTravails' => $experienceDeTravails,
            'competences' => $competences
        ]);
    }

    /**
     * @Route("recuperer_candidats", name="recuperer_candidats")
     */
    public function recupererCandidats(Request $request)
    {
        $idCandidatExpediteur = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)])->getCandidat()->getId();

        $recherche = $request->get('recherche');

        $candidats = $this->getDoctrine()->getRepository(Candidat::class)->findStartingWith($recherche);

        $i = 0;
        $jsonContent = null;
        if ($candidats != null) {
            foreach ($candidats as $candidatDestinataire) {
                if ($candidatDestinataire->getId() != $idCandidatExpediteur) {
                    $jsonContent[$i]["idCandidatExpediteur"] = $idCandidatExpediteur;
                    $jsonContent[$i]["idCandidatDestinataire"] = $candidatDestinataire->getId();
                    $jsonContent[$i]["idPhoto"] = $candidatDestinataire->getIdPhoto();
                    $jsonContent[$i]["nomPrenom"] =
                        $candidatDestinataire->getPrenom() . " " .
                        $candidatDestinataire->getNom();
                    $i++;
                }
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("candidat/recherche", name="recherche_candidats")
     * @throws Exception
     */
    public function rechercheCandidat(Request $request)
    {
        $recherche = $request->get('recherche');

        $candidats = $this->getDoctrine()->getRepository(Candidat::class)->findStartingWith($recherche);

        $i = 0;
        $jsonContent = null;
        if ($candidats != null) {
            foreach ($candidats as $candidat) {
                $jsonContent[$i]["id"] = $candidat->getId();
                $jsonContent[$i]["nom"] = $candidat->getNom();
                $jsonContent[$i]["prenom"] = $candidat->getPrenom();
                $jsonContent[$i]["dateNaissance"] = $candidat->getDateNaissance()->format('d-m-Y');
                $jsonContent[$i]["sexe"] = $candidat->getSexe();
                $jsonContent[$i]["tel"] = $candidat->getTel();
                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("candidat/{idCandidat}/modifier", name="modifier_candidat")
     */
    public function modifierCandidat(Request $request, $idCandidat)
    {
        $manager = $this->getDoctrine()->getManager();
        $candidat = $manager->getRepository(Candidat::class)->find($idCandidat);

        $form = $this->createForm(CandidatType::class, $candidat);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $request->files->get('candidat')['idPhoto'];
            $uploads_directory = $this->getParameter('uploads_directory_candidat');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $candidat->setIdPhoto('images/candidat/uploads/'.$filename);

            $candidat = $form->getData();
            $manager->persist($candidat);
            $manager->flush();

            return $this->redirectToRoute('profil_candidat', ['idCandidat' => $candidat->getId()]);
        }

        return $this->render('front_end/candidat/_profile/modifier.html.twig', [
            'candidat' => $candidat,
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }

    /**
     * @Route("candidat/{idUser}/modifier_email", name="modifier_email")
     */
    public function modifierEmail(Request $request, $idUser)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository(User::class)->find($idUser);

        $form = $this->createForm(UserType::class, $user);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $candidat = $form->getData();
            $manager->persist($candidat);
            $manager->flush();

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('front_end/candidat/modification_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }

    /**
     * @Route("candidat/{idUser}/modifier_mot_de_passe", name="modifier_mot_de_passe")
     */
    public function modifierMotDePasse(Request $request, $idUser)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository(User::class)->find($idUser);

        $form = $this->createForm(UserType::class, $user);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $candidat = $form->getData();
            $manager->persist($candidat);
            $manager->flush();

            return $this->redirectToRoute('afficherProfil');
        }

        return $this->render('front_end/candidat/modification_mot_de_passe.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'manipulation' => "Modifier"
        ]);
    }
}