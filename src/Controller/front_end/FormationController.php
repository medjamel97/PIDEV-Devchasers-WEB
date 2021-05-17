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
        $recherche = $request->get('valeur-recherche');
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findStartingWith($recherche);

        if ($formations) {
            $jsonContent = null;
            $i = 0;
            foreach ($formations as $formation) {
                $jsonContent[$i]['nomSociete'] = $formation->getSociete()->getNom();
                $jsonContent[$i]['idPhotoSociete'] = $formation->getSociete()->getIdPhoto();
                $jsonContent[$i]['adresseSociete'] = $formation->getSociete()->getAdresse();
                $jsonContent[$i]['nom'] = $formation->getNom();
                $jsonContent[$i]['filiere'] = $formation->getFiliere();
                $jsonContent[$i]['debut'] = $formation->getDebut()->format('H:i - d/M/Y');
                $jsonContent[$i]['fin'] = $formation->getFin()->format('H:i - d/M/Y');
                $i++;
            }
            return new Response(json_encode($jsonContent));
        }

        return new Response(null);
    }
}
