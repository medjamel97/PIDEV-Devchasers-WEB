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

/**
 * @Route("like/", name="like")
 */
class LikeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("", name="gererLikes")
     */
    public function gererLikes(Request $request)
    {
        $likeType = (int)$request->get('typeLike');

        $idUtilisateur = $this->session->get("utilisateur")["idUtilisateur"];

        $haveLike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUtilisateur' => $idUtilisateur,
            'typelike' => true,
        ]);
        $haveDislike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUtilisateur' => $idUtilisateur,
            'typelike' => false,
        ]);

        $jsonContent['haveLike'] = $haveLike != null;
        $jsonContent['haveDislike'] = $haveDislike == null;

        if ($likeType == 1) {
            $this->addLike($haveLike, $likeType, $idUtilisateur, $request->get('idPub'));
        } else {
            $this->addLike($haveDislike, $likeType, $idUtilisateur, $request->get('idPub'));
        }

        $nbrlike = $this->getDoctrine()->getRepository(Like::class)->countlikeNumber();
        $like = $this->getDoctrine()->getRepository(Like::class)->countItemNumber();

        if ($like != 0) {
            $pourcentage = ((float)$nbrlike / (float)$like) * 100;
        } else {
            $pourcentage = 0;
        }

        $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($request->get('idPub'));
        $publication->setPourcentageLike($pourcentage);

        $repository = $this->getDoctrine()->getManager();
        $repository->persist($publication);
        $repository->flush();

        $jsonContent['nbLike'] = $nbrlike;
        $jsonContent['nbDislike'] = ($like - $nbrlike);
        $jsonContent['pourcentage'] = $pourcentage;
        return new Response(json_encode($jsonContent));
    }

    function addLike($haveLike, $typeLike, $idUtilisateur, $idPub)
    {

        if ($haveLike == null) {
            $like = new Like();
            $like->setTypelike($typeLike);
            $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idPub);

            $like->setPublication($publication);
            $like->setIdUtilisateur($idUtilisateur);

            $repository = $this->getDoctrine()->getManager();
            $repository->persist($like);
            $repository->flush();

        } else {
            $missionManager = $this->getDoctrine()->getManager();
            $missionManager->remove($haveLike);
            $missionManager->flush();
        }
    }
}