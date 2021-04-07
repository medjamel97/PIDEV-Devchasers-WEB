<?php

namespace App\Controller\back_end;

use App\Entity\OffreDeTravail;
use App\Form\OffreDeTravailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;


class OffreDeTravailController extends Controller
{
    /**
     * @Route("back_end/offreDeTravail/ajouter", name="aff")
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
        return $this->render('/back_end/offreDeTravail/display.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("back_end//delete/{id}", name="del")
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
     * @Route("back_end/offre={id}/modifier", name="modOffre")
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

        return $this->render('/back_end/offreDeTravail/modifierOffre.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("back_end//listoffre", name="listeA")
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
        return $this->render('/back_end/offreDeTravail/listoffre.html.twig', [
            'Offre' => $res,
        ]);
    }

    /**
     * @Route("back_end//searchOffre ", name="searchOffre")
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