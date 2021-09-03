<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\Education;
use App\Entity\User;
use App\Form\EducationType;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("mobile/")
 */
class EducationController extends AbstractController
{
    /**
     * @Route("recuperer_education")
     * @param Request $request
     * @return Response
     */
    public function recupererEducations(Request $request): Response
    {
        $candidatId = (int)$request->get('candidatId');

        $educations = $this->getDoctrine()->getRepository(Education::class)->findBy([
            'candidat' => $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId)
        ]);

        if (!$educations) {
            return new Response(null);
        }

        $jsonContent = null;
        $i = 0;
        foreach ($educations as $education) {
            $jsonContent[$i]['id'] = $education->getId();
            $jsonContent[$i]['description'] = $education->getDescription();
            $jsonContent[$i]['niveauEducation'] = $education->getNiveauEducation();
            $jsonContent[$i]['filiere'] = $education->getFiliere();
            $jsonContent[$i]['etablissement'] = $education->getEtablissement();
            $jsonContent[$i]['ville'] = $education->getVille();
            $jsonContent[$i]['duree'] = $education->getDuree();
            $jsonContent[$i]['idCandidat'] = $education->getCandidat()->getId();

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);
    }

    /**
     * @Route("ajouter_education")
     */
    public function ajouterEducation(Request $request): Response
    {
        $candidatId = (int)$request->get('candidatId');
        $description = $request->get('description');
        $niveauEducation = $request->get('niveauEducation');
        $filiere = $request->get('filiere');
        $etablissement = $request->get('etablissement');
        $ville = $request->get('ville');
        $duree = $request->get('duree');


        $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find($candidatId);

        $education = new Education();

        $education->setCandidat($candidat)->setDescription($description)->setNiveauEducation($niveauEducation)->setFiliere($filiere)->setEtablissement($etablissement)->setVille($ville)->setDuree($duree);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($education);
        $entityManager->flush();

        return (new Response(null));
    }

    /**
     * @Route("modifier_education")
     */
    public function modifierEducation(Request $request, $idEducation)
    {
        return $this->manipulerEducation($request, 'Modifier',
            $this->getDoctrine()->getRepository(Education::class)->find($idEducation));
    }

    public function manipulerEducation(Request $request, $manipulation, $education)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(EducationType::class, $education)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $education = $form->getData();
                $education->setCandidat($user->getCandidat());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($education);
                $entityManager->flush();

                return $this->redirectToRoute('profil_candidat', ['idCandidat' => $user->getCandidat()->getId()]);
            }

            return $this->render('front_end/candidat/education/manipuler.html.twig', [
                'education' => $education,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirectToRoute('connexion');
        }
    }

    /**
     * @Route("supprimer_education")
     */
    public function supprimerEducation(Request $request): Response
    {
        $idEducation = (int)$request->get("id");
        $manager = $this->getDoctrine()->getManager();
        $education = $manager->getRepository(Education::class)->find($idEducation);
        $manager->remove($education);
        $manager->flush();
        return new Response("Suppression effectué");

    }
}
