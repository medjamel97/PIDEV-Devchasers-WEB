<?php

namespace App\Controller\back_end_societe;

use App\Entity\Formation;
use App\Entity\User;
use App\Form\FormationType;
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
class FormationController extends AbstractController
{
    /**
     * @Route("formation")
     */
    public function afficherToutFormation(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->render('back_end_societe/societe/formation/afficher_tout.html.twig', [
            'formations' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Formation::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("formation/{idFormation}/afficher")
     */
    public function afficherFormation($idFormation): Response
    {
        return $this->render('back_end_societe/societe/formation/afficher.html.twig', [
            'formation' => $this->getDoctrine()->getRepository(Formation::class)->find($idFormation),
        ]);
    }

    /**
     * @Route("formation/recherche")
     */
    public function rechercheFormation(Request $request): Response
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

        return $this->render('back_end_societe/societe/formation/indexR.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("formation/ajouter")
     */
    public function ajouterFormation(Request $request): Response
    {
        return $this->ajoutFormation(new Formation(), $request);
    }

    public function ajoutFormation($formation, $request): Response
    {
        $form = $this->createForm(FormationType::class, $formation)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $request->getSession()->get(Security::LAST_USERNAME)
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($formation->getDebut() > $formation->getFin()) {
                $this->addFlash('error', 'La date de fin doit être supérieure a la date de debut');
                return $this->ajoutFormation($formation, $request);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $formation->setSociete($user->getSociete());

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirect('/espace_societe/formation');
        }

        return $this->render('back_end_societe/societe/formation/manipuler.html.twig', [
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
        $form = $this->createForm(FormationType::class, $formation)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($formation->getDebut() > $formation->getFin()) {
                $this->addFlash('error', 'La date de fin doit être supérieure a la date de debut');
                return $this->modifierFormation($request, $idFormation);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect('/espace_societe/formation/' . $idFormation . '/afficher');
        }

        return $this->render('back_end_societe/societe/formation/manipuler.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("formation/{idFormation}/supprimer")
     */
    public function supprimerFormation(Request $request, $idFormation): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($idFormation);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirect('/espace_societe/formation');
    }
}
