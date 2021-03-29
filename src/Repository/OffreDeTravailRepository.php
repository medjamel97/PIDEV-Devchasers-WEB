<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\OffreDeTravail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OffreDeTravail|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreDeTravail|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreDeTravail[]    findAll()
 * @method OffreDeTravail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreDeTravailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreDeTravail::class);
    }

    // /**
    //  * @return OffreDeTravail[] Returns an array of OffreDeTravail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OffreDeTravail
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function finder($categorie_id): ?Categorie
    {

        $em = $this->getEntityManager();

        $query = $this->getEntityManager($categorie_id)
            ->createQuery('
            SELECT a, v FROM OffreDeTravail:Categorie a
            JOIN v.Categorie v
            WHERE a.id = :id'
            )->setParameter('id', $categorie_id);

        return $query->getOneOrNullResult();
    }
    public function findOffreByNsc($job){
        return $this->createQueryBuilder('OffreDeTravail')
        ->where('OffreDeTravail.job LIKE :job')
        ->setParameter('job', '%'.$job.'%')
        ->getQuery()
        ->getResult();
        }
}
