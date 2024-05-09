<?php

namespace App\DataFixtures;

use App\Entity\Patient;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Zenstruck\Foundry\anonymous;

class PatientFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        // patients avec des séjours
        PatientFactory::new()->many(5)->create(
            fn() => [
                'user' => UserFactory::new(),
                'hospitalStays' => HospitalStayFactory::new()->many(1,5)
            ]);
        // patients avec entrées et/sorties aujourd'hui
        HospitalStayFactory::new()->withExistingPatient()->entryToday()->many(3)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->withExistingPatient()->exitToday()->entryToday()->many(1)->create();
    }
}
