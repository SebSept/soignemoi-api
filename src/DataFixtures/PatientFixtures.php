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
        PatientFactory::new()->many(15)->create(
            [
                'user' => UserFactory::new(),
                'hospitalStays' => HospitalStayFactory::new()->many(1,5)
            ]);
    }
}
