<?php

namespace App\Repository;

use App\Entity\Revue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function findByOffre($idOffre)
    {
        return $this->createQueryBuilder('r')
            ->join('r.candidatureOffre', 'c')
            ->where('c.id = r.candidatureOffre')
            ->andWhere('c.offreDeTravail = :val')
            ->setParameter('val', $idOffre)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySociete($idSociete)
    {
        return $this->createQueryBuilder('r')
            ->join('r.candidatureOffre', 'c')
            ->join('c.offreDeTravail', 'o')
            ->where('c.id = r.candidatureOffre')
            ->andWhere('o.id = c.offreDeTravail')
            ->andWhere('o.societe = :val')
            ->setParameter('val', $idSociete)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByOffreAndNbEtoiles($offreDeTravailId, $nbEtoiles)
    {
        return $this->createQueryBuilder('r')
            ->join('r.candidatureOffre', 'c')
            ->where('c.id = r.candidatureOffre')
            ->andWhere('c.offreDeTravail = :val')
            ->andWhere('r.nbEtoiles = :val2')
            ->setParameters(['val' => $offreDeTravailId, 'val2' => $nbEtoiles])
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySocieteAndNbEtoiles($societeId, $nbEtoiles)
    {
        return $this->createQueryBuilder('r')
            ->join('r.candidatureOffre', 'c')
            ->join('c.offreDeTravail', 'o')
            ->where('c.id = r.candidatureOffre')
            ->andWhere('o.id = c.offreDeTravail')
            ->andWhere('o.societe = :val')
            ->andWhere('r.nbEtoiles = :val2')
            ->setParameters(['val' => $societeId, 'val2' => $nbEtoiles])
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
