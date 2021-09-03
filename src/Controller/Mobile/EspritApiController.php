<?php


namespace App\Controller\Mobile;

use App\Entity\Evenement;
use App\Entity\Formation;
use App\Entity\Societe;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Laminas\EventManager\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/espritApi")
 */
class EspritApiController extends AbstractController
{

    /**
     * @Route("/allFormation", methods={"GET"})
     */
    public function allFormation(): Response
    {

        $formations = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();
        $jsonContent = null;
        //     id`, `societe_id`, `nom`, `filiere`, `debut`, `fin`
        $i = 0;
        $formation = new Formation();
        foreach ($formations as $formation) {
            $jsonContent[$i]['id'] = $formation->getId();

            $jsonContent[$i]['societe_id'] = $formation->getSociete()->getId();
            $jsonContent[$i]['nom'] = $formation->getNom();
            $jsonContent[$i]['filiere'] = $formation->getFiliere();
            $jsonContent[$i]['debut'] = $formation->getDebut()->format('Y-m-d H:i:s');

            $jsonContent[$i]['fin'] = $formation->getFin()->format('Y-m-d H:i:s');

            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);

    }

    /**
     * @Route("/allEvent", methods={"GET"})
     */
    public function allEvent(): Response
    {

        $evenements = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->findAll();
        $jsonContent = null;

        $i = 0;
        $evenement = new Evenement();
        foreach ($evenements as $evenement) {
            $jsonContent[$i]['id'] = $evenement->getId();

            $jsonContent[$i]['titre'] = $evenement->getTitre();
            $jsonContent[$i]['societe_id'] = $evenement->getSociete()->getId();
            $jsonContent[$i]['description'] = $evenement->getDescription();
            $jsonContent[$i]['debut'] = $evenement->getDebut()->format('Y-m-d H:i:s');

            $jsonContent[$i]['fin'] = $evenement->getFin()->format('Y-m-d H:i:s');
            $jsonContent[$i]['allDay'] = $evenement->getAllDay();
            $i++;
        }
        $json = json_encode($jsonContent);
        return new Response($json);

    }

}

