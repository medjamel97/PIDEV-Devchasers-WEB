<?php

namespace App\Controller\back_end;

use App\Entity\Evenement;
use App\Entity\User;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("back_end/")
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

        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy(['user' => $user->getId()]);

        $rdvs = [];

        foreach ($evenements as $evenement) {
            $rdvs[] = [
                'id' => $evenement->getId(),
                'id_user' => $evenement->getIdUser(),
                'start' => $evenement->getDebut()->format('Y-m-d H:i:s'),
                'end' => $evenement->getFin()->format('Y-m-d H:i:s'),
                'title' => $evenement->getTitre(),
                'descp' => $evenement->getDescp(),
                'allDay' => $evenement->getAllDay(),

            ];
        }

        $data = json_encode($rdvs);

        return $this->render('back_end/societe/evenement/calendrier.html.twig', compact('data'));
    }

    /**
     * @Route("evenement/{idEvenement}")
     */
    public function afficherEvenement($idEvenement)
    {
        return $this->render('back_end/societe/evenement/afficher.html.twig', [
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

        return $this->render('back_end/societe/evenement/afficher_tout.html.twig', array(
            'evenements' => $evenements
        ));

    }

    /**
     * @Route("evenement/ajouter")
     */
    public function ajouterEvenement(Request $request)
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('back_end/societe/evenement/manipuler.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
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

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('back_end/societe/evenement/manipuler.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
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

        return $this->redirectToRoute('evenement');
    }
}
