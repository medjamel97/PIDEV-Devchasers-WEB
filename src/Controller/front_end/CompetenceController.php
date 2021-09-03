<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Competence;
use App\Entity\User;
use App\Form\CompetenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CompetenceController extends AbstractController
{
    /**
     * @Route("competence/ajouter", name="ajouter_competence")
     */
    public function ajouterCompetence(Request $request)
    {
        return $this->manipulerCompetence($request, 'Ajouter', new Competence());
    }

    /**
     * @Route("competence/{idCompetence}/modifier", name="modifier_competence")
     */
    public function modifierCompetence(Request $request, $idCompetence)
    {
        return $this->manipulerCompetence($request, 'Modifier',
            $this->getDoctrine()->getRepository(Competence::class)->find($idCompetence));
    }

    public function manipulerCompetence(Request $request, $manipulation, $competence)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(CompetenceType::class, $competence)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {
                $competence = $form->getData();
                $competence->setCandidat($user->getCandidat());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($competence);
                $entityManager->flush();

                return $this->redirectToRoute('profil_candidat', ['idCandidat' => $user->getCandidat()->getId()]);
            }

            return $this->render('front_end/candidat/competence/manipuler.html.twig', [
                'competence' => $competence,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirectToRoute('connexion');
        }
    }

    /**
     * @Route("competence/{idCompetence}/supprimer", name="supprimer_competence")
     */
    public function supprimerCompetence($idCompetence): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $competence = $manager->getRepository(Competence::class)->find($idCompetence);
        $manager->remove($competence);
        $manager->flush();

        return $this->redirectToRoute('profil_candidat', ['idCandidat' => $competence->getCandidat()->getId()]);
    }
}
