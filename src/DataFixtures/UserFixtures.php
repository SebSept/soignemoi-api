<?php

namespace App\DataFixtures;

use DateTime;
use App\Factory\DoctorFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // secrétaire - pas de docteur ou patient associé
        UserFactory::new()->create(
            [
                'email' => 'test@test.com',
                'password' => 'hello',
                'roles' => [''],
                'access_token' => UserFactory::VALID_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );

        // docteur
        $user = UserFactory::new()->create(
            [
                'email' => 'doctor@doctor.com',
                'password' => 'hello',
                'roles' => [''],
                'access_token' => UserFactory::VALID_DOCTOR_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );
        $doctor = DoctorFactory::new()->create(['user' => $user]);
    }
}
