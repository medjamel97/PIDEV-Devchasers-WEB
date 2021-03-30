<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\PublicationController;
use App\Entity\Like;


class LikeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    

    /**
     * @Route("/likeeee", name="likeeee")
     */
    public function index(): Response
    {
        return $this->render('like/index.html.twig', [
            'controller_name' => 'LikeController',
        ]);
    }


     /**
     * @Route("like", name="ajoutLike")
     */
    public function ajoutLike(Request $request)
    {
        $idpub = $request->get('idPub');
        $likeType = (boolean)$request->get('typeLike');
        $idUtilisateur = $this->session->get("utilisateur")["idUtilisateur"];

        $haveLike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUtilisateur' => $idUtilisateur,
            'typelike' => true,
            ]);
        $haveDislike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUtilisateur' => $idUtilisateur,
            'typelike' => false,
            ]);
        

        if ($likeType == true){
            if ($haveLike == null){
                $like = new Like();
                $like->setTypelike($likeType);
                $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idpub);
                $like->setPublication($publication);
       
                $like->setIdUtilisateur($idUtilisateur);
       
                $likeRepository = $this->getDoctrine()->getManager();
                $likeRepository->persist($like);
                $likeRepository->flush();

            }else{
                $missionManager = $this->getDoctrine()->getManager();
                $missionManager->remove($haveLike);
                $missionManager->flush();
            }

            return new Response($this->getDoctrine()->getRepository(Like::class)->countlikeNumber());
        }
        else {
            if ($haveDislike == null){
                $like = new Like();
                $like->setTypelike($likeType);
                $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idpub);
       
                $like->setPublication($publication);
                $like->setIdUtilisateur($idUtilisateur);
       
                 $likeRepository = $this->getDoctrine()->getManager();
                 $likeRepository->persist($like);
                $likeRepository->flush();

            }else{
                $missionManager = $this->getDoctrine()->getManager();
                $missionManager->remove($haveDislike);
                $missionManager->flush();
            }

            $nbrlike= $this->getDoctrine()->getRepository(Like::class)->countlikeNumber();
            $like= $this->getDoctrine()->getRepository(Like::class)->countItemNumber();
            return new Response($like-$nbrlike);
        }
    }
}