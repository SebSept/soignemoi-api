<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\ApiResource\StateProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Patient;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class CreatePatient.
 *
 * @implements ProcessorInterface<Patient, Patient>
 */
class CreatePatient implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Override]
    public function process(mixed $patient, Operation $operation, array $uriVariables = [], array $context = []): Patient
    {
        if (!$patient instanceof Patient) {
            throw new Exception('On traite uniquement les Patient avec ce processeur');
        }

        // pas de validation comme on avait dans le controlleur
        // la validation est déjà faite par apiplatform

        // création du user + du patient par cascade
        $user = new User();
        $user->setEmail($patient->userCreationEmail);
        $user->setPassword($this->passwordHasher->hashPassword($user, $patient->userCreationPassword));
        $user->setPatient($patient);

        $this->validator->validate($user);

        $patient->setUser($user);
        $this->entityManager->persist($user);
        $this->entityManager->persist($patient);
        $this->entityManager->flush();

        return $patient;
    }
}
