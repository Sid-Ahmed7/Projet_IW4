<?php

namespace App\Repository;

use App\Entity\DevisAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevisAsset>
 *
 * @method DevisAsset|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisAsset|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisAsset[]    findAll()
 * @method DevisAsset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisAsset::class);
    }

//    /**
//     * @return DevisAsset[] Returns an array of DevisAsset objects
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

//    public function findOneBySomeField($value): ?DevisAsset
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
