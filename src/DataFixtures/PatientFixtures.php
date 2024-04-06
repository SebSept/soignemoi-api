<?php

namespace App\DataFixtures;

use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Zenstruck\Foundry\anonymous;

class PatientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $factory = anonymous(Patient::class);

        $factory->createMany(
            40,
            static fn (): array => [
                'firstname' =>  $faker->firstName(),
                'lastname' =>  $faker->lastName(),
                'address1' =>  $faker->streetAddress(),
                'address2' =>  $faker->postcode.' '.$faker->city(),
                'password' =>  password_hash($faker->password(), null, ['cost' => 4]), // 4 est la plus petite valeur possible.
            ]
        );
    }
}
