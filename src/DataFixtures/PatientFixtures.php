<?php

namespace App\DataFixtures;

use App\Entity\Patient;
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
        // patients non associés à des users
        PatientFactory::new()->many(15)->create();
    }
}
