<?php

namespace App\Repository;

use App\Entity\UserPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPlan>
 *
 * @method UserPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPlan[]    findAll()
 * @method UserPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPlan::class);
    }

//    /**
//     * @return UserPlan[] Returns an array of UserPlan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserPlan
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
