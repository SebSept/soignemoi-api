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
