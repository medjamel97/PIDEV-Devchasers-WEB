<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findMyConversationsSorted($idUserExpediteur)
    {
        return $this->createQueryBuilder('c')
            ->where('c.userExpediteur = :val')
            ->setParameter('val', $idUserExpediteur)
            ->orderBy('c.dateDernierMessage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findDernierMessage($idConvesation) : array
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('m.contenu,m.estProprietaire')
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->setMaxResults(1)
                ->orderBy('m.dateCreation', "DESC")
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            $message['contenu'] = "aucun message";
            $message['estProprietaire'] = false;
            return $message;
        }
    }

    public function findDernierMessageEstVu($idConvesation)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select("m.estVu")
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->setMaxResults(1)
                ->orderBy('m.dateCreation', "DESC")
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return "";
        } catch (NonUniqueResultException $e) {
            return "TOO MANY MSGS";
        }
    }

    public function getNombreMessageNonLues($idConvesation)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('count(m.id)')
                ->join('c.message', 'm')
                ->where('c.id = :val')
                ->setParameter("val", $idConvesation)
                ->andWhere('m.estVu = false')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function findMyConversationSortedStartingWith($recherche, $idUser)
    {
        return $this->createQueryBuilder('c')
            ->where("
            (c.userExpediteur = :val1) AND ( c.nom LIKE :val2)")
            ->setParameters(["val1" => $idUser, "val2" => $recherche . '%'])
            ->orderBy('c.dateDernierMessage', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
