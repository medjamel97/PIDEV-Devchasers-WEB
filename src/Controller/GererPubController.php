<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class GererPubController extends Controller
{
    /**
     * @Route("/gerer/pub", name="gerer_pub")
     */
    public function index(): Response
    {
        return $this->render('gerer_pub/index.html.twig', [
            'controller_name' => 'GererPubController',
        ]);
    }

     /**
     * @Route("/gererpublication", name="gererPublication")
     */
    public function afficherToutPublication(Request $request): Response
    {   
        $em= $this->getDoctrine()->getManager();
        $pubs=$em->getRepository(Publication::class)->findAll();
        
          
     
        $paginator= $this->get('knp_paginator');
      
    
         
        return $this->render('/backEnd/gerer_pub/gererpub.html.twig', [
            'publications' =>$pubs,
            
            ]);
    }



}
