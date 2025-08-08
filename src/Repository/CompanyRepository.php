<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

//    /**
//     * @return Company[] Returns an array of Company objects
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

//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findByCategoryId($categoryId)
{
    return $this->createQueryBuilder('c')
        ->join('c.categorie', 'cat')
        ->where('cat.id = :categoryId')
        ->setParameter('categoryId', $categoryId)
        ->getQuery()
        ->getResult();
}

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si une entreprise existe déjà par son nom (optionnellement par email)
     */
    public function existsByNameOrEmail(string $name, ?string $email = null): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('LOWER(c.name) = :name')
            ->setParameter('name', strtolower($name));
        if ($email !== null) {
            $qb->orWhere('LOWER(c.email) = :email')
               ->setParameter('email', strtolower($email));
        }
        return (int)$qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Trouve les entreprises les plus actives par nombre de devis
     */
    public function findTopCompaniesByDevisCount(int $limit = 10): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.name, COUNT(d.id) as devis_count, SUM(d.amount) as total_amount')
            ->leftJoin('c.devis', 'd')
            ->groupBy('c.id, c.name')
            ->orderBy('devis_count', 'DESC')
            ->addOrderBy('total_amount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(function($row) {
            return [
                'name' => $row['name'],
                'devis_count' => (int)$row['devis_count'],
                'total_amount' => (float)$row['total_amount'] ?: 0
            ];
        }, $result);
    }
}
