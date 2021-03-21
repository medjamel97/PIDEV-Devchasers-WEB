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
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findLastMessages($idConvesation, $maxResults)
    {
        $countResult = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->join('m.conversation', 'c')
            ->where('c.id = :val')
            ->setParameter("val",$idConvesation)
            ->getQuery()
            ->getSingleScalarResult();

        if ($countResult > $maxResults) {
            $fistResult = $countResult - $maxResults;
        } else {
            $fistResult = 0;
        }

        return $this->createQueryBuilder('m')
            ->join('m.conversation', 'c')
            ->where('c.id = :val')
            ->setParameter("val",$idConvesation)
            ->setFirstResult($fistResult)
            ->setMaxResults($maxResults)
            ->orderBy('m.dateCreation',"ASC")
            ->getQuery()
            ->getResult();
    }
}
