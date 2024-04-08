<?php

declare(strict_types=1);

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
