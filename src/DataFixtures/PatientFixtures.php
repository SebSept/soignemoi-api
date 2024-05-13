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
//        PatientFactory::new()->many(5)->create(
//            fn() => [
//                'user' => UserFactory::new(),
//                'hospitalStays' => HospitalStayFactory::new()->many(1,5)
//            ]);
        // patients avec entrées et/sorties aujourd'hui
//         entrées à faire
        HospitalStayFactory::new()
            ->withNewPatient()
            ->entryToday(false)
            ->many(2)->create();
//        // entrées faites
        HospitalStayFactory::new()
            ->withNewPatient()
            ->entryToday()
            ->many(3)->create();

        // sorties faites
        HospitalStayFactory::new()
            ->withNewPatient(withPrescriptions: true, withMedicalOpinions: true)
            ->exitToday()
            ->many(1,3)->create();
        // sorties à faire
        HospitalStayFactory::new()
            ->withNewPatient(withPrescriptions: true, withMedicalOpinions: true)
            ->exitToday(false)
            ->many(2)->create();

    }
}
