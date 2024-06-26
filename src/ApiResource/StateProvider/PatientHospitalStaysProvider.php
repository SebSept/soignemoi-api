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
use App\Entity\User;
use App\Repository\HospitalStayRepository;
use Exception;
use Override;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class PatientHospitalStaysProvider.
 *
 * @implements ProviderInterface<HospitalStay>
 */
readonly class PatientHospitalStaysProvider implements ProviderInterface
{
    public function __construct(
        private HospitalStayRepository $hospitalStayRepository,
        private Security $security,
    ) {
    }

    #[Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var ?User $user */
        $user = $this->security->getUser();
        $patient = $user?->getPatient();
        // fait doublon avec les droits d'accès, mais on garde par sécurité, pour le debogage.
        if (is_null($patient)) {
            throw new Exception('Pas de patient associé au user.');
        }

        return $this->hospitalStayRepository->findBy(['patient' => $patient]);
    }
}
