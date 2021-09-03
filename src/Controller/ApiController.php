<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Evenement;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    /**
     * @Route("api/{id}/edit", name="api_event_edit", methods={"GET"})
     */
    public function majEvent(?Evenement $evenement, Request $request)
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->end) && !empty($donnees->end)
        ) {
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if (!$evenement) {
                // On instancie un rendez-vous
                $evenement = new Evenement();

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $evenement->setTitre($donnees->title);
            $evenement->setDescription($donnees->description);
            try {
                $evenement->setDebut(new DateTime($donnees->start));
            } catch (Exception $e) {
            }
            if ($donnees->allDay) {
                try {
                    $evenement->setFin(new DateTime($donnees->start));
                } catch (Exception $e) {
                }
            } else {
                try {
                    $evenement->setFin(new DateTime($donnees->end));
                } catch (Exception $e) {
                }
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
