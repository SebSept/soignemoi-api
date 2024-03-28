<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\MedicalOpinion;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\anonymous;
use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\repository;

class MedicalOpinionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $doctorRepository = repository(Doctor::class);
        $patientRepository = repository(Patient::class);
        $factory = anonymous(MedicalOpinion::class);
        $factory->createMany(100, fn () => [
            'title' => faker()->sentence(rand(1, 4)),
            'description' => faker()->sentence(rand(1, 30)),
            'date' => faker()->dateTimeBetween('-4 months'),
            'doctor' => $doctorRepository->random(),
            'patient' => $patientRepository->random(),
        ]);
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
        ];
    }
}
