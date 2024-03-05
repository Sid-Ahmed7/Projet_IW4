<?php

namespace App\Repository;

use App\Entity\Reque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reque>
 *
 * @method Reque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reque[]    findAll()
 * @method Reque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reque::class);
    }

    //    /**
    //     * @return Reque[] Returns an array of Reque objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reque
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Dans votre RequeRepository



}
