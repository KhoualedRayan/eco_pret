<?php

namespace App\Repository;

use App\Entity\AnnonceMateriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnonceMateriel>
 *
 * @method AnnonceMateriel|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnonceMateriel|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnonceMateriel[]    findAll()
 * @method AnnonceMateriel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceMaterielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnonceMateriel::class);
    }

//    /**
//     * @return AnnonceMateriel[] Returns an array of AnnonceMateriel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnnonceMateriel
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
