<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function findLastMessages($idConvesation, $maxResults, $page)
    {

        try {
            $countResult = $this->createQueryBuilder('m')
                ->select('count(m.id)')
                ->join('m.conversation', 'c')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }


        if ($countResult > ($maxResults * $page)) {
            $fistResult = $countResult - ($maxResults * $page);
        } else {
            $maxResults = $maxResults - (($maxResults * $page) - $countResult);
            $fistResult = 0;
        }

        if ($maxResults < 0) {
            $maxResults = 0;
        }

        return $this->createQueryBuilder('m')
            ->join('m.conversation', 'c')
            ->where('c.id = :val')
            ->setParameter("val", $idConvesation)
            ->setFirstResult($fistResult)
            ->setMaxResults($maxResults)
            ->orderBy('m.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUnseenMessages($idConvesation)
    {
        return $this->createQueryBuilder('m')
            ->join('m.conversation', 'c')
            ->where('c.id = :val')
            ->andWhere('m.estVu = false')
            ->setParameter("val", $idConvesation)
            ->getQuery()
            ->getResult();
    }
}
