<?php

namespace App\Repository;

use App\Entity\Revue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Revue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Revue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Revue[]    findAll()
 * @method Revue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revue::class);
    }

    // /**
    //  * @return Revue[] Returns an array of Revue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findSinglePageResults($firstResult,$maxResults)
    {
        return $this->createQueryBuilder('r')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
            ;
    }

    public function countItemNumber()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->getQuery()
            ->getResult()
            ->getSingleScalarResult();
    }
}
