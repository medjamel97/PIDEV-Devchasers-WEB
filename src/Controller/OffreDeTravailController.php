<?php

namespace App\Controller;

use App\Entity\OffreDeTravail;
use App\Entity\Categorie;
use App\Form\OffreDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OffreDeTravailController extends Controller
{

    /**
     * @Route("/offreDeTravail", name="aff")
     */
    public function addOffre(Request $request)
    {
        $offre = new OffreDeTravail();

        $form = $this->createForm(OffreDeTravailType::class, $offre)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offre);
            $em->flush();
            $this->addFlash(
                'Success', 'added successfully'
            );

            return $this->redirectToRoute('listeA');
        }
        return $this->render('/backEnd/offreDeTravail/afficher.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="del")
     */
    public function deloffre(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(OffreDeTravail::class)->find($id);
        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('listeA');
    }

    /**
     * @Route("offre={id}/modifier", name="modOffre")
     */
    public function modifierOffre(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $offre = $em->getRepository(OffreDeTravail::class)->find($id);

        $form = $this->createForm(OffreDeTravailType::class, $offre)
            ->add('Modifier', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listeA');
        }

        return $this->render('/backEnd/offreDeTravail/modifierOffre.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/listoffres", name="liste")
     */
    public function LireOffre(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Offre = $em->getRepository(OffreDeTravail::class)->findAll();

        $paginator = $this->get('knp_paginator');
        $res = $paginator->paginate(
            $Offre,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );
        return $this->render('/frontEnd/offreDeTravail/listoffres.html.twig', [
            'Offre' => $res,
        ]);
    }

    /**
     * @Route("/listoffre", name="listeA")
     */
    public function LireOffers(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $Offre = $em->getRepository(OffreDeTravail::class)->findAll();

        $paginator = $this->get('knp_paginator');
        $res = $paginator->paginate(
            $Offre,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );
        return $this->render('/backEnd/offreDeTravail/listoffre.html.twig', [
            'Offre' => $res,
        ]);
    }

    /**
     * @Route("/listOffre/categorie={id}", name="list")
     */
    public function show(int $id, Request $request): Response
    {
        $offre = $this->getDoctrine()
            ->getRepository(OffreDeTravail::class)
            ->findBy(['categorie' => $this->getDoctrine()->getRepository(Categorie::class)->find($id)]);

        $paginator = $this->get('knp_paginator');
        $res = $paginator->paginate(
            $offre,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 3)
        );

        return $this->render('/frontEnd/offreDeTravail/listoffres.html.twig', [
            'Offre' => $res,
        ]);
    }

    /**
     * @Route("/searchOffre ", name="searchOffre")
     */
    public function searchOffre(Request $request, NormalizerInterface $Normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(OffreDeTravail::class);
        $requestString = $request->get('searchValue');
        $offres = $repository->findOffreByNsc($requestString);
        $jsonContent = $Normalizer->normalize($offres, 'json', ['groups' => 'get:read']);

        return new Response(json_encode($jsonContent));
    }
}