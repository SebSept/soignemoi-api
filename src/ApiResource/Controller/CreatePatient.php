<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\ApiResource\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\Patient;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreatePatient extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(Patient $patient): Patient
    {
        $violations = $this->validator->validate($patient);
        if (0 !== count($violations)) {
            throw new ValidationException((string) $violations->get(0)->getMessage());
        }

        // création du user + du patient par cascade
        $user = new User();
        $user->setEmail($patient->userCreationEmail);
        $user->setPassword($this->passwordHasher->hashPassword($user, $patient->userCreationPassword));
        $user->setPatient($patient);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $patient->setUser($user);

        return $patient;
    }
}
