<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\anonymous;
use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\repository;

class PrescriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $factory = anonymous(Prescription::class);
        $doctorRepository = repository(Doctor::class);
        $patientRepository = repository(Patient::class);

        $prescriptions = $factory->createMany(150, fn () => [
            'date' => faker()->dateTimeBetween('-4 months'),
            'doctor' => $doctorRepository->random(),
            'patient' => $patientRepository->random(),
        ]);

        $this->generateAndGetItems($prescriptions);
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
        ];
    }

    private function generateAndGetItems(array $prescriptions): void
    {
        $factory = anonymous(PrescriptionItem::class);
        $factory->createMany(count($prescriptions) * (rand(1, 2)), fn () => [
            'drug' => faker()->word(),
            'dosage' => faker()->words(3, true),
            'prescription' => faker()->randomElement($prescriptions),
        ]);
    }
}
