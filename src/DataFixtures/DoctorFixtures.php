<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\anonymous;
use function Zenstruck\Foundry\faker;

class DoctorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $factory = anonymous(Doctor::class);
        $factory->createMany(12, fn () =>
        [
            'firstname' => faker()->firstName,
            'lastname' => faker()->lastName,
            'medicalSpeciality' => faker()
                ->randomElement([
                    'cardilolgie', 'oncologie', 'dermatologie', 'pédiatrie', 'gynécologie', 'urologie', 'neurologie', 'psychiatrie', 'ophtalmologie', 'ORL']),
            'employeeId' => faker()->numerify('##-####-###'),
            'password' => password_hash(faker()->unique()->password(), null, ['cost' => 4]),
        ]);
    }
}
