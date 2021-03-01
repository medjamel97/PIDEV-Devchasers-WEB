<?php

namespace App\Controller;

    use App\Entity\Revue;
    use App\Form\RevueType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

class RevueController extends AbstractController
{
    /**
     * @Route("/revue", name="revue")
     */
    public function index(): Response
    {
        return $this->render('/frontEnd/revue/afficher.html.twig', [
            'controller_name' => 'RevueController',
        ]);
    }

    /**
     * @Route("/revue/afficher", name="afficherRevue")
     */
    public function afficherRevue()
    {
        return $this->render('/frontEnd/revue/afficher.html.twig', [
            'Revue' => $this->getDoctrine()->getRepository(Revue::class)->findAll(),
        ]);
    }

    /**
     * @Route("/revue/afficherRevueSociete/{pageIndex}", name="afficherRevueSociete")
     */
    public function afficherRevueSociete(Request $request, $pageIndex)
    {
        $em = $this->getDoctrine()->getManager()->getRepository(Revue::class);

        $maxResult = 6;

        $results = $em ->findSinglePageResults($pageIndex,$maxResult);

        return $this->render('/frontEnd/revue/afficherRevueSociete.html.twig',[
            'Revue' => $results,
            'maxResults' => $maxResult,
            'nbResults' => $em->countItemNumber(),
            'nbPages' => intdiv($em->countItemNumber() , $maxResult),
            'activePage' => 1,
        ]);
    }

    /**
     * @Route("/revue/ajouter", name="ajouterRevue")
     */
    public function ajouterRevue(Request $request)
    {
        $revue = new Revue();

        $form = $this->createForm(RevueType::class, $revue)
            ->add('Ajouter', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $revue = $form->getData();

            $RevueRepository = $this->getDoctrine()->getManager();
            $RevueRepository->persist($revue);
            $RevueRepository->flush();

            return $this->redirectToRoute('afficherRevueSociete', ['pageIndex' => 1]);
        }

        return $this->render('/frontEnd/revue/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/revue/modifier/{id}", name="modifierRevue")
     */
    public function modifierRevue(Request $request, $id)
    {
        $RevueRepository = $this->getDoctrine()->getManager();
        $revue = $RevueRepository->getRepository(Revue::class)->find($id);

        $form = $this->createForm(RevueType::class, $revue);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $RevueRepository->flush();
            return $this->redirectToRoute('afficherRevueSociete', ['pageIndex' => 1]);
        }

        return $this->render('/frontEnd/revue/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/revue/supprimer/page={page}/{id}", name="supprimerRevue")
     */
    public function supprimerRevue($id,$page)
    {
        $RevueRepository = $this->getDoctrine()->getManager();
        $revue = $RevueRepository->getRepository(Revue::class)->find($id);
        $RevueRepository->remove($revue);
        $RevueRepository->flush();
        return $this->redirectToRoute('afficherRevueSociete', ['pageIndex' => $page]);
    }
}
