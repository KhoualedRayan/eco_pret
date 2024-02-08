<?php

namespace App\Repository;

use App\Entity\DBEco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DBEco>
 *
 * @method DBEco|null find($id, $lockMode = null, $lockVersion = null)
 * @method DBEco|null findOneBy(array $criteria, array $orderBy = null)
 * @method DBEco[]    findAll()
 * @method DBEco[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DBEcoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DBEco::class);
    }

//    /**
//     * @return DBEco[] Returns an array of DBEco objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DBEco
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
