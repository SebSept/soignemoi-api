<?php

namespace App\Repository;

use App\Entity\HospitalStay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HospitalStay>
 *
 * @method HospitalStay|null find($id, $lockMode = null, $lockVersion = null)
 * @method HospitalStay|null findOneBy(array $criteria, array $orderBy = null)
 * @method HospitalStay[]    findAll()
 * @method HospitalStay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalStayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, HospitalStay::class);
    }

    //    /**
    //     * @return HospitalStay[] Returns an array of HospitalStay objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HospitalStay
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
