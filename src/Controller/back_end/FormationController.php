<?php

namespace App\Controller\back_end;

use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("back_end/")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("formation")
     */
    public function afficherToutFormation(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('back_end/societe/formation/afficher_tout.html.twig', [
            'formations' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Formation::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("formation/{idFormation}/afficher")
     */
    public function afficherFormation($idFormation)
    {
        return $this->render('back_end/societe/formation/afficher.html.twig', [
            'formation' => $this->getDoctrine()->getRepository(Formation::class)->find($idFormation),
        ]);
    }

    /**
     * @Route("formation/recherche")
     */
    public function rechercheFormation(Request $request)
    {
        $formation = $request->get('formation');
        $em = $this->getDoctrine()->getManager();
        if ($formation == "") {
            $formations = $em->getRepository(Formation::class)->findAll();
        } else {
            $formations = $em->getRepository(Formation::class)->findBy(
                ['nom' => $formation]
            );
        }

        return $this->render('back_end/societe/formation/indexR.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("formation/ajouter")
     */
    public function ajouterFormation(Request $request)
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $formation->setSociete($user->getSociete());

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirect('/back_end/formation');
        }

        return $this->render('back_end/societe/formation/manipuler.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'manipulation' => 'Ajouter',
        ]);
    }

    /**
     * @Route("formation/{idFormation}/modifier")
     */
    public function modifierFormation(Request $request, $idFormation)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($idFormation);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/back_end/formation');
        }

        return $this->render('back_end/societe/formation/manipuler.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("formation/{idFormation}/supprimer")
     */
    public function supprimerFormation(Request $request, $idFormation)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($idFormation);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirect('/back_end/formation');
    }
}
