<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\ApiResource\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use DateTime;

/**
 * @implements ProviderInterface<HospitalStay>
 */
readonly class HospitalStayTodayEntries implements ProviderInterface
{
    public function __construct(private HospitalStayRepository $hospitalStayRepository)
    {
    }

    /**
     * @return HospitalStay[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        return $this->hospitalStayRepository->findBy(['startDate' => new DateTime()]);
    }
}
