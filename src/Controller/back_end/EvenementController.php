<?php

namespace App\Controller\back_end;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
class EvenementController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("evenement", name="afficher_tout_evenement_back", methods={"GET"})
     */
    public function afficherToutEvenement()
    {
        $idUtilisateur = $this->session->get("utilisateur")['idUtilisateur'];

        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findBy(['utilisateur' => $idUtilisateur]);

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
     * @Route("evenement/{idEvenement}", name="afficher_evenement")
     */
    public function afficherEvenement($idEvenement)
    {
        return $this->render('back_end/societe/evenement/afficher.html.twig', [
            'evenement' => $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement),
        ]);
    }

    /**
     * @Route("evenement/searchevenement", name="evenement_search")
     */
    public function searchEvenement(Request $request)
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

        return $this->render('back_end/societe/evenement/indexR.html.twig', array(
            'evenements' => $evenements
        ));

    }

    /**
     * @Route("evenement/ajouter", name="ajouter_evenement")
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

        return $this->render('back_end/societe/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("evenement/{idEvenement}/modifier", name="modifier_evenement")
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

        return $this->render('back_end/societe/evenement/modifier.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("evenement/{idEvenement}/supprimer", name="supprimer_evenement")
     */
    public function supprimerEvenement(Request $request, $idEvenement)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($idEvenement);

        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('');
    }
}
