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


    public function findByOffre($idOffre)
    {
        return $this->createQueryBuilder('i')
            ->join('i.candidatureOffre', 'c')
            ->where('c.id = i.candidatureOffre')
            ->andWhere('c.offreDeTravail = :val')
            ->setParameter('val', $idOffre)
            ->orderBy('i.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySociete($idSociete)
    {
        return $this->createQueryBuilder('i')
            ->join('i.candidatureOffre', 'c')
            ->join('c.offreDeTravail', 'o')
            ->where('c.id = i.candidatureOffre')
            ->andWhere('o.id = c.offreDeTravail')
            ->andWhere('o.societe = :val')
            ->setParameter('val', $idSociete)
            ->orderBy('i.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByOffreAndDifficulte($offreDeTravailId, $difficulte)
    {
        return $this->createQueryBuilder('i')
            ->join('i.candidatureOffre', 'c')
            ->where('c.id = i.candidatureOffre')
            ->andWhere('c.offreDeTravail = :val')
            ->andWhere('i.difficulte = :val2')
            ->setParameters(['val' => $offreDeTravailId, 'val2' => $difficulte])
            ->orderBy('i.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySocieteAndDifficulte($societeId, $difficulte)
    {
        return $this->createQueryBuilder('i')
            ->join('i.candidatureOffre', 'c')
            ->join('c.offreDeTravail', 'o')
            ->where('c.id = i.candidatureOffre')
            ->andWhere('o.id = c.offreDeTravail')
            ->andWhere('o.societe = :val')
            ->andWhere('i.difficulte = :val2')
            ->setParameters(['val' => $societeId, 'val2' => $difficulte])
            ->orderBy('i.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
