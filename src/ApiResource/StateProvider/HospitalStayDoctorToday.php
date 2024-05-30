<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\ApiResource\StateProvider;

use Override;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\HospitalStay;
use App\Repository\HospitalStayRepository;
use RuntimeException;

/**
 * Class HospitalStayDoctorToday.
 *
 * @implements ProviderInterface<HospitalStay>
 */
class HospitalStayDoctorToday implements ProviderInterface
{
    public function __construct(private readonly HospitalStayRepository $hospitalStayRepository)
    {
    }

    /**
     * @return HospitalStay[]
     */
    #[Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        if (!isset($uriVariables['doctor_id'])) {
            throw new RuntimeException('la variable doctor_id devrait être définie.');
        }

        assert(is_int($uriVariables['doctor_id']));

        return $this->hospitalStayRepository->findByDoctorForToday($uriVariables['doctor_id']);
    }
}
