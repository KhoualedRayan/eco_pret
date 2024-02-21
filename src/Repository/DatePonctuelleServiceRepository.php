<?php

namespace App\Repository;

use App\Entity\DatePonctuelleService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatePonctuelleService>
 *
 * @method DatePonctuelleService|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatePonctuelleService|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatePonctuelleService[]    findAll()
 * @method DatePonctuelleService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatePonctuelleServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatePonctuelleService::class);
    }

//    /**
//     * @return DatePonctuelleService[] Returns an array of DatePonctuelleService objects
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

//    public function findOneBySomeField($value): ?DatePonctuelleService
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
