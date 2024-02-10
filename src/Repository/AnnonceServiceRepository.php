<?php

namespace App\Repository;

use App\Entity\AnnonceService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnonceService>
 *
 * @method AnnonceService|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnonceService|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnonceService[]    findAll()
 * @method AnnonceService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnonceService::class);
    }

//    /**
//     * @return AnnonceService[] Returns an array of AnnonceService objects
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

//    public function findOneBySomeField($value): ?AnnonceService
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
