<?php

namespace App\Controller;

use App\Repository\HospitalStayRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayTodayEntries
{
    public function __construct(private HospitalStayRepository $repository)
    {
    }

    public function __invoke()
    {
        return $this->repository->findBy(['startDate' => new \DateTime()]);
    }


}
