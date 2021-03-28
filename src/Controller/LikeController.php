<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\PublicationController;
use App\Entity\Like;


class LikeController extends AbstractController
{
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
     * @Route("/ajouterlike/type={likeType}/publication={idpub}", name="like")
     */
    public function ajouterLike($likeType, $idpub)
    {
        if (1==1){
        $like = new Like();
        $like->setTypelike($likeType);
        $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idpub);
        $like->setPublication($publication);
       
        $like->setIdUtilisateur(1);
       
        $likeRepository = $this->getDoctrine()->getManager();
        $likeRepository->persist($like);
        $likeRepository->flush();
        }
        return new Response(null);
    }


     
    
}
