<?php

namespace App\Repository;

use App\Entity\ExperienceDeTravail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExperienceDeTravail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExperienceDeTravail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExperienceDeTravail[]    findAll()
 * @method ExperienceDeTravail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceDeTravailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExperienceDeTravail::class);
    }

    // /**
    //  * @return ExperienceDeTravail[] Returns an array of ExperienceDeTravail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneBySomeField($idCandidat)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.candidat = :val')
            ->setParameter('val', $idCandidat)
            ->getQuery()
            ->getResult();
    }

}
