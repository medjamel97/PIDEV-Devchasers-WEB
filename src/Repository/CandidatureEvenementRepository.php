<?php

namespace App\Repository;

use App\Entity\CandidatureEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CandidatureEvenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatureEvenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatureEvenement[]    findAll()
 * @method CandidatureEvenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureEvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatureEvenement::class);
    }

    // /**
    //  * @return CandidatureEvenement[] Returns an array of CandidatureEvenement objects
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
    public function findOneBySomeField($value): ?CandidatureEvenement
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
