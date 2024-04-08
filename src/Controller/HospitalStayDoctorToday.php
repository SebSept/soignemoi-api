<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Controller;

use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayDoctorToday
{
    public function __construct(private HospitalStayRepository $hospitalStayRepository)
    {
    }

    /**
     * @return HospitalStay[]
     */
    public function __invoke(?int $doctor_id = null): array
    {
        return $this->hospitalStayRepository->findBy(
            [
                'doctor' => $doctor_id,
            ]);
    }
}
