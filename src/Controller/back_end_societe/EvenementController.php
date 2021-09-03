<?php

namespace App\Controller\back_end_societe;

use App\Entity\Evenement;
use App\Entity\User;
use App\Form\EvenementType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("espace_societe/")
 */
class EvenementController extends AbstractController
{
    /**
     * @Route("evenement")
     */
    public function afficherToutEvenement(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->render('back_end_societe/societe/evenement/afficher_tout.html.twig', [
            'evenements' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Evenement::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("evenement/{idEvenement}/afficher")
     */
    public function afficherEvenement($idEvenement): Response
    {
        return $this->render('back_end_societe/societe/evenement/afficher.html.twig', [
            'evenement' => $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement),
        ]);
    }


    /**
     * @Route("evenement/calendrier")
     */
    public function calendrier(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy(['societe' => $user->getSociete()]);

        $rendezVous = [];

        foreach ($evenements as $evenement) {
            $rendezVous[] = [
                'title' => $evenement->getTitre(),
                'id' => $evenement->getId(),
                'societe' => $evenement->getSociete()->getId(),
                'start' => $evenement->getDebut()->format('Y-m-d H:i'),
                'end' => $evenement->getFin()->format('Y-m-d H:i'),
                'description' => $evenement->getDescription(),
                'allDay' => $evenement->getAllDay(),

            ];
        }

        $data = json_encode($rendezVous);
        return $this->render('back_end_societe/societe/evenement/calendrier.html.twig', compact('data'));
    }

    /**
     * @Route("evenement/recherche")
     */
    public function rechercheEvenement(Request $request): Response
    {
        $evenement = $request->get('evenement');
        $em = $this->getDoctrine()->getManager();
        if ($evenement == "") {
            $evenements = $em->getRepository(Evenement::class)->findAll();
        } else {
            $evenements = $em->getRepository(Evenement::class)->findBy(
                ['titre' => $evenement]
            );
        }

        return $this->render('back_end_societe/societe/evenement/afficher_tout.html.twig', [
            'evenements' => $evenements
        ]);

    }

    /**
     * @Route("evenement/ajouter")
     */
    public function ajouterEvenement(Request $request): Response
    {
        return $this->ajoutEvenement(new Evenement(), $request);
    }

    public function ajoutEvenement($evenement, $request): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $request->getSession()->get(Security::LAST_USERNAME)
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($evenement->getDebut() > $evenement->getFin()) {
                $this->addFlash('error', 'La date de fin doit être supérieure a la date de debut');
                return $this->ajoutEvenement($evenement, $request);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $evenement->setSociete($user->getSociete());

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirect('/espace_societe/evenement');
        }

        return $this->render('back_end_societe/societe/evenement/manipuler.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter',
        ]);
    }

    /**
     * @Route("evenement/{idEvenement}/modifier")
     */
    public function modifierEvenement(Request $request, $idEvenement)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement);
        $form = $this->createForm(EvenementType::class, $evenement)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($evenement->getDebut() > $evenement->getFin()) {
                $this->addFlash('error', 'La date de fin doit être supérieure a la date de debut');
                return $this->modifierEvenement($request, $idEvenement);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/espace_societe/evenement/' . $idEvenement . '/afficher');
        }

        return $this->render('back_end_societe/societe/evenement/manipuler.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("evenement/{idEvenement}/supprimer")
     */
    public function supprimerEvenement(Request $request, $idEvenement): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirect('/espace_societe/evenement');
    }
}
