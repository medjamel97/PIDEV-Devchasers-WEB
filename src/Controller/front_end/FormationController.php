<?php

namespace App\Controller\front_end;

use App\Entity\Formation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    /**
     * @Route("formation", name="afficher_tout_formation")
     */
    public function afficherToutFormation(Request $request, PaginatorInterface $paginator)
    {
        return $this->render('front_end/societe/formation/afficher_tout.html.twig', [
            'formations' => $paginator->paginate(
                $this->getDoctrine()->getRepository(Formation::class)->findAll(),
                $request->query->getInt('page', 1), 3
            ),
        ]);
    }

    /**
     * @Route("formation/recherche", name="recherche_formation")
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

        return new Response(null);
    }
}
