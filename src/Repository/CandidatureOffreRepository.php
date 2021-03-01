<?php

namespace App\Repository;

use App\Entity\CandidatureOffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CandidatureOffre|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatureOffre|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatureOffre[]    findAll()
 * @method CandidatureOffre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureOffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatureOffre::class);
    }

    // /**
    //  * @return CandidatureOffre[] Returns an array of CandidatureOffre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CandidatureOffre
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
