<?php

namespace App\Controller\Mobile;

use App\Entity\Candidat;
use App\Entity\User;
use App\Entity\CandidatureOffre;
use App\Entity\Commentaire;
use App\Entity\OffreDeTravail;
use App\Entity\Publication;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("mobile/")
 */
class CommentaireController extends AbstractController
{

    /**
     * @Route("recuperer_commentaires_by_pub")
     * @return Response
     */
    public function recupererCommentaires(Request $request)
    {
        $idPublication = (int)$request->get("id");

        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($idPublication);

        $commentaires = $this->getDoctrine()->getManager()
        ->getRepository(Commentaire::class)->findBy(['publication' => $publication]);
        if($commentaires) {

        
        $jsonContent = null;
        $i = 0;
        foreach ($commentaires as $commentaire) {
            $jsonContent[$i]['id'] = $commentaire->getId();
            $jsonContent[$i]['userId'] = $commentaire->getUser()->getId();
            $jsonContent[$i]['description'] = $commentaire->getDescription();
            $jsonContent[$i]['publicationId'] = $commentaire->getPublication()->getId();

            $i++;
        }

        
        if ($jsonContent != null){
        return new Response(json_encode($jsonContent));
    }
    }throw new Exception ("Bye") ; 
    }

    /**
     * @Route("manipuler_commentaire")
     * @throws Exception
     */
    public function manipulerCommentaire(Request $request)
    {
        $idCommentaire = (int)$request->get("id");

        if ($idCommentaire == null) {
            $commentaire = new Commentaire();
            $idUser = (int)$request->get("idUser");
            $idPub = (int)$request->get("idPub");
            
            $commentaire->setUser(
           
                 $this->getDoctrine()->getRepository(User::class)->find($idUser)
            
                
            )->setPublication(   $this->getDoctrine()->getRepository(Publication::class)->find($idPub));
        }
        //Modifier commentaire
        else {
            $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($idCommentaire);
        }

  
        $description = $request->get("description");

        $commentaire
           
            ->setDescription($description);
          

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($commentaire);
        $manager->flush();

        return new Response("Ajout/Modification commentaire effectué");
    }

    /**
     * @Route("supprimer_commentaire")
     * @param Request $request
     * @return Response
     */
    public function supprimerCommentaire(Request $request)
    {
        $idCommentaire = (int)$request->get("id");

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($this->getDoctrine()->getRepository(Commentaire::class)->find($idCommentaire));
        $manager->flush();

        return new Response("Suppression effectué");
    }

 /**
     * @Route("ajouter_commentaire")
     * @param Request $request
     * @return Response
     */
    public function ajouterCommentaire(Request $request)
    {
        $idUser = $this->getDoctrine()->getRepository(User::class)->find($request->get('idUser'))->getId();

        $commentaire = new Commentaire();
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($request->get('idPub'));
        $commentaire
            ->setDescription($request->get('description'))
            ->setPublication($publication)
            ->setUser($this->getDoctrine()->getRepository(User::class)->find($idUser));

        $commentaireRepository = $this->getDoctrine()->getManager();
        $commentaireRepository->persist($commentaire);
        $commentaireRepository->flush();

        return new Response("Ajout effectué");
    }


}
