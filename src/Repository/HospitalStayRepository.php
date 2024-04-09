<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

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

    /**
     * @return array<HospitalStay>
     */
    public function findByDoctorForToday(int $doctor_id): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.doctor = :doctor_id')
            ->andWhere('h.checkin IS NOT NULL')
            ->andWhere('h.checkout IS NULL')
            ->setParameter('doctor_id', $doctor_id)
            ->getQuery()
            ->getResult();
    }
}
