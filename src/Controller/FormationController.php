<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/formation")
 */
class FormationController extends Controller
{
    /**
     * @Route("/", name="formation_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {

        $formations = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        // Paginate the results of the query
        $paginator = $this->get('knp_paginator');
        $formations = $paginator->paginate(
        // Doctrine Query, not results
            $formations,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('backEnd/societe/formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }


    /**
     * @Route("/indexF", name="formation_indexF", methods={"GET"})
     */
    public function indexF(Request $request): Response
    {

        $formations = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        // Paginate the results of the query
        $paginator = $this->get('knp_paginator');
        $formations = $paginator->paginate(
        // Doctrine Query, not results
            $formations,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('frontEnd/utilisateur/societe/formation/indexFront.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * @Route("/searchformation", name="formation_search")
     */
    public function searchFormation(Request $request)
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

        return $this->render('backEnd/societe/formation/indexR.html.twig', array(
            'formations' => $formations
        ));

    }

    /**
     * @Route("/searchFrontformation", name="formationF_search")
     */
    public function searchFrontFormation(Request $request)
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

        return $this->render('frontEnd/utilisateur/societe/formation/indexFrontR.html.twig', array(
            'formations' => $formations
        ));

    }

    /**
     * @Route("/listFPDF", name="formation_listPdf", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function indexpdf(): Response
    {    // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $formations = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('frontEnd/utilisateur/societe/formation/listFormationPDF.html.twig', [
            'formations' => $formations,
        ]);

        //  $formations = $fo
        //rmationRepository->findAll();

        // Retrieve the HTML generated in our twig file
//        $html = $this->renderView('formation/listFormationPDF.html.twig', [
//            'formations' => $formations,
//        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);

        return new Response(null);
    }


    /**
     * @Route("/new", name="formation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('formation_index');
        }

        return $this->render('backEnd/societe/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formation_show", methods={"GET"})
     */
    public function show(Formation $formation): Response
    {
        return $this->render('backEnd/societe/formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="formation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('formation_index');
        }

        return $this->render('backEnd/societe/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formation_delete", methods={"POST"})
     */
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('formation_index');
    }

    /**
     * @Route("/listformation", name="formation_listPdf", methods={"GET"})
     */
    public function listFormationPDF(): Response
    {


    }
}
