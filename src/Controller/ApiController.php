<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\EvenementZO;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("api/{id}/edit", name="api_event_edit", methods={"GET"})
     */
    public function majEvent(?EvenementZO $evenement, Request $request)
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->descp) && !empty($donnees->descp) &&
            isset($donnees->end) && !empty($donnees->end)
        ) {
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if (!$evenement) {
                // On instancie un rendez-vous
                $evenement = new EvenementZO();

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $evenement->setTitre($donnees->title);
            $evenement->setDescp($donnees->descp);
            $evenement->setDebut(new DateTime($donnees->start));
            if ($donnees->allDay) {
                $evenement->setFin(new DateTime($donnees->start));
            } else {
                $evenement->setFin(new DateTime($donnees->end));
            }
            $evenement->setAllDay($donnees->allDay);


            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();

            // On retourne le code  ;
            return new Response('Ok', $code);
        } else {
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }


        return $this->redirectToRoute('evenement_index');
    }

}