<?php

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 */
class WalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    public function findByCompany($company): ?Wallet
    {
        return $this->findOneBy(['company' => $company]);
    }

    public function findWalletsWithBalance(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.balance > :zero')
            ->setParameter('zero', '0.00')
            ->orderBy('w.balance', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalBalanceAcrossAllWallets(): string
    {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.balance) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }

    public function getTotalPendingBalanceAcrossAllWallets(): string
    {
        $result = $this->createQueryBuilder('w')
            ->select('SUM(w.pendingBalance) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }
}
