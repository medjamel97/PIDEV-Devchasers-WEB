<?php

namespace App\Controller\front_end;

use App\Entity\Like;
use App\Entity\Publication;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class LikeController extends AbstractController
{
    /**
     * @Route("like", name="like")
     */
    public function like(Request $request)
    {
        $idUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)])->getId();

        $likeType = (int)$request->get('typeLike');
        $idPublication = $request->get('idPub');

        $haveLike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUser' => $idUser,
            'typeLike' => true,
        ]);
        $haveDislike = $this->getDoctrine()->getRepository(Like::class)->findOneBy([
            'idUser' => $idUser,
            'typeLike' => false,
        ]);

        $jsonContent['haveLike'] = $haveLike != null;
        $jsonContent['haveDislike'] = $haveDislike == null;

        if ($likeType == 1) {
            $this->addLike($haveLike, $likeType, $idUser, $request->get('idPub'));
        } else {
            $this->addLike($haveDislike, $likeType, $idUser, $request->get('idPub'));
        }

        $nombreObjets = $this->getDoctrine()->getRepository(Like::class)->nombreObjets($idPublication);
        $nombreLikes = $this->getDoctrine()->getRepository(Like::class)->nombreLikes($idPublication);
        if ($nombreLikes != 0) {
            $pourcentage = ($nombreLikes / $nombreObjets) * 100;
        } else {
            $pourcentage = 0;
        }

        $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idPublication);
        $publication->setPourcentageLike($pourcentage);

        $repository = $this->getDoctrine()->getManager();
        $repository->persist($publication);
        $repository->flush();

        $jsonContent['nbLike'] = $nombreLikes;
        $jsonContent['nbDislike'] = ($nombreObjets - $nombreLikes);
        $jsonContent['pourcentage'] = $pourcentage;
        return new Response(json_encode($jsonContent));
    }

    function addLike($haveLike, $typeLike, $idUser, $idPub)
    {

        if ($haveLike == null) {
            $like = new Like();
            $like->setTypeLike($typeLike);
            $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idPub);

            $like->setPublication($publication);
            $like->setIdUser($idUser);

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