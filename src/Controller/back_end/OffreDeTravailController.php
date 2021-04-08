<?php

namespace App\Controller\back_end;

use App\Entity\OffreDeTravail;
use App\Form\OffreDeTravailType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("back_end/")
 */
class OffreDeTravailController extends AbstractController
{
    /**
     * @Route("offre_de_travail")
     */
    public function afficherToutOffreDeTravailBack(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('back_end/offre_de_travail/afficher.html.twig', [
            'offreDeTravails' => $paginator->paginate(
                $this->getDoctrine()->getRepository(OffreDeTravail::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("offre_de_travail/ajouter")
     */
    public function addOffre(Request $request)
    {
        $offreDeTravail = new OffreDeTravail();

        $form = $this->createForm(OffreDeTravailType::class, $offreDeTravail)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offreDeTravail);
            $em->flush();
            $this->addFlash(
                'Success', 'added successfully'
            );

            return $this->redirectToRoute('listeA');
        }
        return $this->render('back_end/offre_de_travail/manipuler.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("offre/{idOffreDeTravail}/modifier")
     */
    public function modifierOffreDeTravail(Request $request, $idOffreDeTravail)
    {
        $em = $this->getDoctrine()->getManager();
        $offreDeTravail = $em->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);

        $form = $this->createForm(OffreDeTravailType::class, $offreDeTravail)
            ->add('Modifier', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listeA');
        }

        return $this->render('back_end/offre_de_travail/manipuler.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("offre_de_travail/{idOffreDeTravail}/supprimer")
     */
    public function supprimerOffreDeTravail($idOffreDeTravail)
    {
        $manager = $this->getDoctrine()->getManager();
        $entity = $manager->getRepository(OffreDeTravail::class)->find($idOffreDeTravail);
        $manager->remove($entity);
        $manager->flush();

        return $this->redirectToRoute('afficher_tout_offre_de_travail');
    }
}