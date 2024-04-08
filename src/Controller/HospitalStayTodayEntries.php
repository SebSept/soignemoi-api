<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use DateTime;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayTodayEntries
{
    public function __construct(private HospitalStayRepository $hospitalStayRepository)
    {
    }

    /**
     * @return HospitalStay[]
     */
    public function __invoke(): array
    {
        return $this->hospitalStayRepository->findBy(['startDate' => new DateTime()]);
    }
}
