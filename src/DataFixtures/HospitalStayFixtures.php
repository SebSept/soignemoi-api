<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\HospitalStay;
use App\Entity\Patient;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use DateTime;
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
        // rien ici car on créé les Séjours quand on créé des patients
        // cf PatientFactory::new()
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
        ];
    }
}
