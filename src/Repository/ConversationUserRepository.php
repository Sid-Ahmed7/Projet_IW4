<?php

namespace App\Repository;

use App\Entity\ConversationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConversationUser>
 *
 * @method ConversationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConversationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConversationUser[]    findAll()
 * @method ConversationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationUser::class);
    }

//    /**
//     * @return ConversationUser[] Returns an array of ConversationUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ConversationUser
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
