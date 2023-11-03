<?php

namespace App\Repository;

use App\Entity\ReviewResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReviewResponse>
 *
 * @method ReviewResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewResponse[]    findAll()
 * @method ReviewResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewResponse::class);
    }

//    /**
//     * @return ReviewResponse[] Returns an array of ReviewResponse objects
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

//    public function findOneBySomeField($value): ?ReviewResponse
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
