<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findSinglePageResults($firstResult, $maxResults,$idCandidatExpediteur,$idCandidatDestinataire)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.candidatExpediteur = :val')
            ->setParameter( 'val', $idCandidatExpediteur)
            ->andWhere('m.candidatDestinataire = :val')
            ->setParameter( 'val', $idCandidatDestinataire)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('m.dateCreation','ASC')
            ->getQuery()
            ->getResult();
    }
}
