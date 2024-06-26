<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }
    public function findByClientOrPosteur(UserInterface $user)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.annonce', 'a')
            ->where('t.client = :user')
            ->orWhere('a.posteur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findByPandEnded(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb->leftJoin('t.annonce', 'a')
                    ->where($qb->expr()->orX("t.statut_transaction = 'Terminer'", "t.statut_transaction = 'FINI'"))
                    ->andWhere('a.posteur = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();
    }

    
    public function findTransactionsAsOffrant(UserInterface $user): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.annonce', 'a')
            ->where('a.posteur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
