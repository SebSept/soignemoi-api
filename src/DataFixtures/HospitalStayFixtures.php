<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Doctor;
use App\Entity\HospitalStay;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\anonymous;
use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\repository;

class HospitalStayFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $objectManager): void
    {

        $factory = anonymous(HospitalStay::class);
        $randomDateGenerator = static fn () => faker()->dateTimeBetween('-4 months', '4 months');

        $doctorRepository = repository(Doctor::class);
        $patientRepository = repository(Patient::class);

        $factory->createMany(
            60,
            static function () use ($randomDateGenerator, $doctorRepository, $patientRepository): array {
                $startDate = $randomDateGenerator();
                $endDate = (clone $startDate)->modify('+' . faker()->numberBetween(0, 5) . ' days');
                $checkIn = null;
                $checkOut = null;
                if($startDate <= new DateTime()) {
                    $checkIn = (clone $startDate)->modify('+' . faker()->numberBetween(6, 12) . ' hours');
                }

                if($endDate <= (new DateTime())->modify('+1 day')) {
                    $checkOut = (clone $endDate)->modify('+' . faker()->numberBetween(13, 23) . ' hours');
                }

                return [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'checkIn' => $checkIn,
                    'checkOut' => $checkOut,
                    'reason' => faker()->sentence(),
                    'medicalSpeciality' => faker()->randomElement(['cardilolgie', 'oncologie', 'dermatologie', 'pédiatrie', 'gynécologie', 'urologie', 'neurologie', 'psychiatrie', 'ophtalmologie', 'ORL']),
                    'doctor' => $doctorRepository->random(),
                    'patient' => $patientRepository->random()
                ];
            }
        );

    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class
        ];
    }
}
