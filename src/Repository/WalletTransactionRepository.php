<?php

namespace App\Repository;

use App\Entity\WalletTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WalletTransaction>
 */
class WalletTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WalletTransaction::class);
    }

    public function findByWallet($wallet, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('wt')
            ->where('wt.wallet = :wallet')
            ->setParameter('wallet', $wallet)
            ->orderBy('wt.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('wt')
            ->where('wt.type = :type')
            ->setParameter('type', $type)
            ->orderBy('wt.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySource(string $source): array
    {
        return $this->createQueryBuilder('wt')
            ->where('wt.source = :source')
            ->setParameter('source', $source)
            ->orderBy('wt.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTransactionStats($wallet, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('wt')
            ->select('wt.type, wt.source, COUNT(wt.id) as count, SUM(wt.amount) as total')
            ->where('wt.wallet = :wallet')
            ->andWhere('wt.createdAt BETWEEN :start AND :end')
            ->setParameter('wallet', $wallet)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('wt.type', 'wt.source')
            ->getQuery()
            ->getResult();
    }

    public function getTotalCredits($wallet, \DateTime $startDate = null, \DateTime $endDate = null): string
    {
        $qb = $this->createQueryBuilder('wt')
            ->select('SUM(wt.amount) as total')
            ->where('wt.wallet = :wallet')
            ->andWhere('wt.type = :type')
            ->setParameter('wallet', $wallet)
            ->setParameter('type', WalletTransaction::TYPE_CREDIT);

        if ($startDate && $endDate) {
            $qb->andWhere('wt.createdAt BETWEEN :start AND :end')
               ->setParameter('start', $startDate)
               ->setParameter('end', $endDate);
        }

        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?? '0.00';
    }

    public function getTotalDebits($wallet, \DateTime $startDate = null, \DateTime $endDate = null): string
    {
        $qb = $this->createQueryBuilder('wt')
            ->select('SUM(wt.amount) as total')
            ->where('wt.wallet = :wallet')
            ->andWhere('wt.type = :type')
            ->setParameter('wallet', $wallet)
            ->setParameter('type', WalletTransaction::TYPE_DEBIT);

        if ($startDate && $endDate) {
            $qb->andWhere('wt.createdAt BETWEEN :start AND :end')
               ->setParameter('start', $startDate)
               ->setParameter('end', $endDate);
        }

        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?? '0.00';
    }
}
