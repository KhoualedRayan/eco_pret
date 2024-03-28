<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

   /**
    * @return Annonce[] Returns an array of Annonce objects
    */
   public function findByTitre($value): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.titre LIKE :val')
           ->setParameter('val', '%'.$value.'%')
           ->getQuery()
           ->getResult()
       ;
   }

   /**
    * @return Annonce[] Returns an array of Annonce objects
    */
    public function findByDescription($value): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.description LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByTitreOrDescription($value, $limit, $offset): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb = $qb
            ->where("a.statut = 'Disponible'")
            ->andWhere($qb->expr()->orX('a.description LIKE :val', 'a.titre LIKE :val'))
            ->orderBy('a.date_publication', 'DESC')
            ->setParameter('val', '%'.$value.'%');
        if ($limit != null)
            $qb->setMaxResults($limit);
        if ($offset != null)
            $qb->setFirstResult($offset);
        return $qb->getQuery()->getResult()
        ;
    }
    public function getCountByTD($value): int
    {
        $qb = $this->createQueryBuilder('a');
        $qb = $qb
            ->select('COUNT(a.id)')
            ->where("a.statut = 'Disponible'")
            ->andWhere($qb->expr()->orX('a.description LIKE :val', 'a.titre LIKE :val'))
            ->orderBy('a.date_publication', 'DESC')
            ->setParameter('val', '%'.$value.'%');
        $res = $qb->getQuery()->getResult()[0][1];
        return $res;
        ;
    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
