<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\PrescriptionItem;
use App\Factory\PrescriptionFactory;
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
        PrescriptionFactory::new()->createMany(50);
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
        ];
    }
}
