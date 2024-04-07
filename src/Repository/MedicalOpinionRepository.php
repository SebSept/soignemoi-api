<?php

namespace App\Repository;

use App\Entity\MedicalOpinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalOpinion>
 *
 * @method MedicalOpinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalOpinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalOpinion[]    findAll()
 * @method MedicalOpinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalOpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, MedicalOpinion::class);
    }

    //    /**
    //     * @return MedicalOpinion[] Returns an array of MedicalOpinion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MedicalOpinion
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
