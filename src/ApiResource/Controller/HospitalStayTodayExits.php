<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\ApiResource\Controller;

use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use DateTime;
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
