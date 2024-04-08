<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\MedicalOpinion;
use App\Entity\Patient;
use App\Factory\MedicalOpinionFactory;
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
        MedicalOpinionFactory::new()->createMany(20);
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
        ];
    }
}
