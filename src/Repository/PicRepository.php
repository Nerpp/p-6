<?php

namespace App\Repository;

use App\Entity\Pic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pic[]    findAll()
 * @method Pic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pic::class);
    }

    // /**
    //  * @return Pic[] Returns an array of Pic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pic
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
