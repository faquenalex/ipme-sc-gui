<?php

namespace App\Repository;

use App\Entity\CachedElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CachedElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method CachedElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method CachedElement[]    findAll()
 * @method CachedElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CachedElementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CachedElement::class);
    }

    // /**
    //  * @return CachedElement[] Returns an array of CachedElement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CachedElement
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
