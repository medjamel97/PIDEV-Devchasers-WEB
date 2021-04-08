<?php

namespace App\Controller\front_end;

use App\Entity\Education;
use App\Entity\User;
use App\Form\EducationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EducationController extends AbstractController
{
    /**
     * @Route("education/ajouter", name="ajouter_education")
     */
    public function ajouterEducation(Request $request)
    {
        return $this->manipulerEducation($request, 'Ajouter', new Education());
    }

    /**
     * @Route("education/{idEducation}/modifier", name="modifier_education")
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
     * @Route("education/{idEducation}/supprimer", name="supprimer_education")
     */
    public function supprimerEducation($idEducation)
    {
        $manager = $this->getDoctrine()->getManager();
        $education = $manager->getRepository(Education::class)->find($idEducation);
        $manager->remove($education);
        $manager->flush();

        return $this->redirectToRoute('profil_candidat', ['idCandidat' => $education->getCandidat()->getId()]);
    }
}
