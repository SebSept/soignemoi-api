<?php

namespace App\Controller;

use App\Repository\HospitalStayRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class HospitalStayTodayExits
{
    public function __construct(private HospitalStayRepository $repository)
    {
    }

    public function __invoke()
    {
        return $this->repository->findBy(['endDate' => new \DateTime()]);
    }


}
