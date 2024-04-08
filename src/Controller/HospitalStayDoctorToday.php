<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Doctor;
use App\Repository\HospitalStayRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayDoctorToday
{
    public function __construct(private HospitalStayRepository $hospitalStayRepository) { }

    public function __invoke(int $doctor_id = null): array
    {
        throw new \Exception('spécifier la date d interval et le status de la sortie ou de l entrée');
        return $this->hospitalStayRepository->findBy(
            [
                'doctor' => $doctor_id,

                ]);
    }

}