<?php

namespace App\DataFixtures;

use App\Factory\DoctorFactory;
use App\Factory\PatientFactory;
use App\Factory\UserFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        // admin
        UserFactory::new()->admin()->create();

        // secrétaire - pas de docteur ou patient associé
        UserFactory::new()->create(
            [
                'email' => 'secretaire@secretaire.com',
                'password' => 'hello',
                'roles' => [],
                'access_token' => UserFactory::VALID_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );

        // docteur
        $user = UserFactory::new()->create(
            [
                'email' => 'doctor@doctor.com',
                'password' => 'hello',
                'roles' => [],
                'access_token' => UserFactory::VALID_DOCTOR_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );
        DoctorFactory::new()
            ->withHospitalStays()
            ->create(['user' => $user]);


        // Patient
        $user = UserFactory::new()->create(
                 [
                    'email' => 'patient@patient.com',
                    'password' => 'hello',
                    'roles' => [],
                    'access_token' => UserFactory::VALID_PATIENT_TOKEN,
                    'token_expiration' => new DateTime('+30 day'),
                ]
            );
        PatientFactory::new()->withHospitalStays()->create(['user' => $user]);

    }
}
