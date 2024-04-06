<?php

namespace App\Controller;

use DateTime;
use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayTodayEntries
{
    public function __construct(private HospitalStayRepository $repository)
    {
    }

    /**
     * @return HospitalStay[]
     */
    public function __invoke(): array
    {
        return $this->repository->findBy(['startDate' => new DateTime()]);
    }


}
