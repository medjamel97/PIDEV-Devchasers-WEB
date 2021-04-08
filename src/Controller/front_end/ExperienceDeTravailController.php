<?php

namespace App\Controller\front_end;

use App\Entity\ExperienceDeTravail;
use App\Entity\User;
use App\Form\ExperienceDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ExperienceDeTravailController extends AbstractController
{
    /**
     * @Route("experience_de_travail/ajouter", name="ajouter_experience_de_travail")
     */
    public function ajouterExperienceDeTravail(Request $request)
    {
        return $this->manipulerExperienceDeTravail($request, 'Ajouter', new ExperienceDeTravail());
    }

    /**
     * @Route("experience_de_travail/{idExperienceDeTravail}/modifier", name="modifier_experience_de_travail")
     */
    public function modifierExperienceDeTravail(Request $request, $idExperienceDeTravail)
    {
        return $this->manipulerExperienceDeTravail($request, 'Modifier',
            $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail));
    }

    public function manipulerExperienceDeTravail(Request $request, $manipulation, $experienceDeTravail)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(ExperienceDeTravailType::class, $experienceDeTravail)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $experienceDeTravail = $form->getData();
                $experienceDeTravail->setCandidat($user->getCandidat());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($experienceDeTravail);
                $entityManager->flush();

                return $this->redirectToRoute('profil_candidat', ['idCandidat' => $user->getCandidat()->getId()]);
            }

            return $this->render('front_end/candidat/experience_de_travail/manipuler.html.twig', [
                'experienceDeTravail' => $experienceDeTravail,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirectToRoute('connexion');
        }
    }

    /**
     * @Route("experience_de_travail/{idExperienceDeTravail}/supprimer", name="supprimer_experience_de_travail")
     */
    public function supprimerExperienceDeTravail($idExperienceDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();
        $experienceDeTravail = $manager->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);
        $manager->remove($experienceDeTravail);
        $manager->flush();

        return $this->redirectToRoute('profil_candidat', ['idCandidat' => $experienceDeTravail->getCandidat()->getId()]);
    }
}
