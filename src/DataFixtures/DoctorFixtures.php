<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use function Zenstruck\Foundry\anonymous;

class DoctorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $factory = anonymous(Doctor::class);
        $factory->createMany(
            10,
            [
                'firstname' => $faker->unique()->firstName(),
                'lastname' => $faker->unique()->lastName(),
                'medicalSpecialty' => $faker->randomElement(['cardilolgie','oncologie', 'dermatologie', 'pédiatrie', 'gynécologie', 'urologie', 'neurologie', 'psychiatrie', 'ophtalmologie', 'ORL']),
                'employeeId' => $faker->numerify('##-####-###'),
                'password' => password_hash($faker->unique()->password(), null, ['cost' => 4]), // 4 est la plus petite valeur possible.
            ]
        );
    }
}
