<?php

namespace App\DataFixtures;

use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PrescriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        PrescriptionFactory::new()->many(10,20)->create(
            function() {
                // on part d'un hospital stay pour la cohérence
                $hs = HospitalStayFactory::repository()->random();
                return [
                    'patient' => $hs->object()->getPatient(),
                    'doctor' => $hs->object()->getDoctor()
                ];
            }
        );
    }

    public function getDependencies(): array
    {
        return [
            DoctorFixtures::class,
            PatientFixtures::class,
            HospitalStayFixtures::class,
        ];
    }
}
