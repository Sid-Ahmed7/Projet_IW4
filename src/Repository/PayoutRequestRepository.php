<?php

namespace App\Repository;

use App\Entity\PayoutRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayoutRequest>
 */
class PayoutRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayoutRequest::class);
    }

    public function findPendingRequests(): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.status = :status')
            ->setParameter('status', PayoutRequest::STATUS_PENDING)
            ->orderBy('pr.requestedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByWallet($wallet): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.wallet = :wallet')
            ->setParameter('wallet', $wallet)
            ->orderBy('pr.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.status = :status')
            ->setParameter('status', $status)
            ->orderBy('pr.requestedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalAmountByStatus(string $status): string
    {
        $result = $this->createQueryBuilder('pr')
            ->select('SUM(pr.amount) as total')
            ->where('pr.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }

    public function getMonthlyPayoutStats(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('pr')
            ->select('pr.status, COUNT(pr.id) as count, SUM(pr.amount) as total')
            ->where('pr.requestedAt BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('pr.status')
            ->getQuery()
            ->getResult();
    }
}
