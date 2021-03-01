<?php

namespace App\Repository;

use App\Entity\Interview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Interview|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interview|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interview[]    findAll()
 * @method Interview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interview::class);
    }

    // /**
    //  * @return InterviewController[] Returns an array of InterviewController objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InterviewController
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
