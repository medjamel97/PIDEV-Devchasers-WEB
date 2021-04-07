<?php

namespace App\Controller\front_end;

use App\Entity\Candidat;
use App\Entity\ExperienceDeTravail;
use App\Form\ExperienceDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ExperienceDeTravailController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("offre_de_travail/ajouter", name="ajouter_offre_de_travail")
     */
    public function ajouterExperienceDeTravail(Request $request)
    {
        $offreDeTravail = new ExperienceDeTravail();
        $form = $this->createForm(ExperienceDeTravailType::class, $offreDeTravail)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $offreDeTravail = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $offreDeTravail->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offreDeTravail);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/offre_de_travail/manipulation.html.twig', [
            'offreDeTravail' => $offreDeTravail,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter'
        ]);
    }

    /**
     * @Route("offre_de_travail/{idExperienceDeTravail}/modifier", name="modifier_offre_de_travail")
     */
    public function modifierExperienceDeTravail(Request $request, $idExperienceDeTravail)
    {
        $offreDeTravail = $this->getDoctrine()->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);

        $form = $this->createForm(ExperienceDeTravailType::class, $offreDeTravail)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $offreDeTravail = $form->getData();

            $candidat = $this->getDoctrine()->getRepository(Candidat::class)->find(
                $this->session->get("utilisateur")["idCandidat"]
            );
            $offreDeTravail->setCandidat($candidat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offreDeTravail);
            $entityManager->flush();

            return $this->redirectToRoute('profil_candidat');
        }

        return $this->render('front_end/candidat/offre_de_travail/manipulation.html.twig', [
            'offreDeTravail' => $offreDeTravail,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("offre_de_travail/{idExperienceDeTravail}/supprimer", name="supprimer_offre_de_travail")
     */
    public function supprimerExperienceDeTravail($idExperienceDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();
        $offreDeTravail = $manager->getRepository(ExperienceDeTravail::class)->find($idExperienceDeTravail);
        $manager->remove($offreDeTravail);
        $manager->flush();

        return $this->redirectToRoute('profil_candidat');
    }
}
