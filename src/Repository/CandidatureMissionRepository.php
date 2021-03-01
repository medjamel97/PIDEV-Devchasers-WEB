<?php

namespace App\Repository;

use App\Entity\CandidatureMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CandidatureMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatureMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatureMission[]    findAll()
 * @method CandidatureMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatureMission::class);
    }

    // /**
    //  * @return CandidatureMission[] Returns an array of CandidatureMission objects
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
    public function findOneBySomeField($value): ?CandidatureMission
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
