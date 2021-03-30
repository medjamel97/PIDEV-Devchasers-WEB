<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findMyConversationsSorted($idCandidatExpediteur)
    {
        return $this->createQueryBuilder('c')
            ->where('c.candidatExpediteur = :val')
            ->setParameter('val', $idCandidatExpediteur)
            ->orderBy('c.dateDernierMessage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findDernierMessage($idConvesation)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select("m.contenu")
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->setMaxResults(1)
                ->orderBy('m.dateCreation', "DESC")
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return "TOO MANY MSGS";
        }
    }

    public function findDernierMessageEstVu($idConvesation)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select("m.estVu")
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->setMaxResults(1)
                ->orderBy('m.dateCreation', "DESC")
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return "";
        } catch (NonUniqueResultException $e) {
            return "TOO MANY MSGS";
        }
    }

    public function getNombreMessageNonLues($idConvesation)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('count(m.id)')
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->andWhere('m.estVu = false')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function findMyConversationSortedStartingWith($recherche, $idCandidat)
    {
        return $this->createQueryBuilder('c')
            ->join("c.candidatExpediteur", "exp")
            ->join("c.candidatDestinataire", "dest")
            ->where("
            (exp.id = :val1) AND 
            ( (dest.nom LIKE 'Myriam') OR (dest.prenom LIKE :val2) )")
            ->setParameters(["val1" => $idCandidat, "val2" => $recherche . '%'])
            ->orderBy('c.dateDernierMessage', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
