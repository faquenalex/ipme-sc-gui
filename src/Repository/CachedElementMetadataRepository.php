<?php

namespace App\Repository;

use App\Entity\CachedElementMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CachedElementMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method CachedElementMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method CachedElementMetadata[]    findAll()
 * @method CachedElementMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CachedElementMetadataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CachedElementMetadata::class);
    }

    // /**
    //  * @return CachedElementMetadata[] Returns an array of CachedElementMetadata objects
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
    public function findOneBySomeField($value): ?CachedElementMetadata
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
