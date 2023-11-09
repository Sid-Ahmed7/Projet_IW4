<?php

namespace App\Repository;

use App\Entity\Requests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Requests>
 *
 * @method Requests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requests[]    findAll()
 * @method Requests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requests::class);
    }

//    /**
//     * @return Request[] Returns an array of Request objects
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

//    public function findOneBySomeField($value): ?Request
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
