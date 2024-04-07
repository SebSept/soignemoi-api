<?php

namespace App\Controller;

use DateTime;
use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayTodayExits
{
    public function __construct(private HospitalStayRepository $hospitalStayRepository)
    {
    }

    /**
     * @return array<int, HospitalStay>
     */
    public function __invoke(): array
    {
        return $this->hospitalStayRepository->findBy(['endDate' => new DateTime()]);
    }


}
