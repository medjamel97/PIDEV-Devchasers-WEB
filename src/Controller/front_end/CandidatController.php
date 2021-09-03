<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\Education;
use App\Entity\ExperienceDeTravail;
use App\Entity\User;
use App\Form\CandidatType;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class CandidatController extends AbstractController
{
    /**
     * @Route("candidat/{idCandidat}/profil", name="profil_candidat")
     */
    public function profile($idCandidat): Response
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
     * @Route("candidat/recherche", name="recherche_candidats")
     * @throws Exception
     */
    public function rechercheCandidat(Request $request): Response
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
    public function modifierCandidat(Request $request, $idCandidat, UserPasswordEncoderInterface $passwordEncoder)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user->getCandidat()->getId() != $idCandidat) {
            throw new Error("Vous ne pouvez pas modifier les informations des autres candidats !");
        }

        $manager = $this->getDoctrine()->getManager();
        $candidat = $manager->getRepository(Candidat::class)->find($idCandidat);
        $user = $manager->getRepository(User::class)->find([
            'id' => $candidat->getUser()->getId(),
        ]);

        $form = $this->createForm(CandidatType::class, $candidat);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $file = $request->files->get('candidat')['idPhoto'];
                $uploads_directory = $this->getParameter('uploads_directory_candidat');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $uploads_directory,
                    $filename
                );
                $candidat->setIdPhoto('http://localhost/khedemti/images/candidat/' . $filename);
            } catch (Error $e) {

            }

            $candidat = $form->getData();
            $manager->persist($user);
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
}
