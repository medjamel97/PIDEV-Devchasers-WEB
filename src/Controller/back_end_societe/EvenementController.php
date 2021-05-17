<?php

namespace App\Controller\back_end_societe;

use App\Entity\Evenement;
use App\Entity\User;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function afficherToutEvenement(Request $request)
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy(['societe' => $user->getSociete()]);

        $rendezVous = [];

        foreach ($evenements as $evenement) {
            $rendezVous[] = [
                'id' => $evenement->getId(),
                'societe' => $evenement->getSociete()->getId(),
                'start' => $evenement->getDebut()->format('H:i - d/M/Y'),
                'end' => $evenement->getFin()->format('H:i - d/M/Y'),
                'title' => $evenement->getTitre(),
                'description' => $evenement->getDescription(),
                'allDay' => $evenement->getAllDay(),

            ];
        }

        $data = json_encode($rendezVous);

        return $this->render('back_end_societe/societe/evenement/calendrier.html.twig', compact('data'));
    }

    /**
     * @Route("evenement/{idEvenement}/afficher")
     */
    public function afficherEvenement($idEvenement)
    {
        return $this->render('back_end_societe/societe/evenement/afficher.html.twig', [
            'evenement' => $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement),
        ]);
    }

    /**
     * @Route("evenement/recherche")
     */
    public function rechercheEvenement(Request $request)
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
    public function ajouterEvenement(Request $request)
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $evenement->setSociete($user->getSociete());

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirect('/back_end_societe/evenement');
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
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/back_end_societe/evenement');
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
    public function supprimerEvenement(Request $request, $idEvenement)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirect('/back_end_societe/evenement');
    }
}
