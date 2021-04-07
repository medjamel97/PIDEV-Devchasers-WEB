<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\Education;
use App\Form\EducationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class EducationController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("education/ajouter", name="ajouter_education")
     */
    public function ajouterEducation(Request $request)
    {
        $education = new Education();
        $form = $this->createForm(EducationType::class, $education)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $education = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $education->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($education);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/education/manipulation.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter'
        ]);
    }

    /**
     * @Route("education/{idEducation}/modifier", name="modifier_education")
     */
    public function modifierEducation(Request $request, $idEducation)
    {
        $education = $this->getDoctrine()->getRepository(Education::class)->find($idEducation);

        $form = $this->createForm(EducationType::class, $education)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $education = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $education->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($education);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/education/manipulation.html.twig', [
            'education' => $education,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
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

        return $this->redirectToRoute('profil_candidat');
    }
}
