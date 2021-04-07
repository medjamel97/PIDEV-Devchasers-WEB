<?php

namespace App\Controller\front_end;

use App\Entity\Like;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("like", name="like")
     */
    public function like(Request $request)
    {
        $likeType = (int)$request->get('typeLike');

        $idUtilisateur = $this->session->get("utilisateur")["idUtilisateur"];
        $idPublication = $request->get('idPub');

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

        $itemNumber = $this->getDoctrine()->getRepository(Like::class)->itemNumber($idPublication);
        $likeNumber = $this->getDoctrine()->getRepository(Like::class)->likeNumber($idPublication);
        if ($likeNumber != 0) {
            $pourcentage = ($likeNumber / $itemNumber) * 100;
        } else {
            $pourcentage = 0;
        }

        $publication = $this->getDoctrine()->getManager()->getRepository(Publication::class)->find($idPublication);
        $publication->setPourcentageLike($pourcentage);

        $repository = $this->getDoctrine()->getManager();
        $repository->persist($publication);
        $repository->flush();

        $jsonContent['nbLike'] = $likeNumber;
        $jsonContent['nbDislike'] = ($itemNumber - $likeNumber);
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