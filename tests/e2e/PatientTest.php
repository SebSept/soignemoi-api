<?php

namespace App\Tests\e2e;

use DateTime;
use Exception;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCreatePatient(): void
    {
        static::createClient()->request('POST', '/api/patients', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Accept' => 'application/ld+json',
            ],
            'json' => [
                'firstname' => 'newuser',
                'lastname' => 'Doe',
                'address1' => '1 rue de la paix',
                'address2' => '75000 Paris',
                // champs pour la création du user
                'userCreationEmail' => 'pass@email.com',
                'userCreationPassword' => 'password-verx-y7-strang'
            ]
            ]
        );

        $this->assertResponseIsSuccessful();
        // 2 entités créés
        UserFactory::assert()->count(1);
        PatientFactory::assert()->count(1);

        $user = UserFactory::repository()->first();
        $this->assertTrue($user->object()->isPatient());
//        $this->assertSame($patient->object(), $user->object()->getPatient());
//        $this->assertInstanceOf(User::class, $patient->object()->getUser());
    }
    
    public function testPatientViewItsHospitalStays(): void
    {
        // Arrange - 2 patients avec des hospital_stays chacun
        $patientUser = $this->makePatientUser();
        $patient = $patientUser->getPatient();

        HospitalStayFactory::new()->many(2)->create(['patient' => $patient]);

        $otherPatient = PatientFactory::new()->create();
        HospitalStayFactory::new()->many(3)->create(['patient' => $otherPatient->object()]);

        // Act
        $client = static::createClientWithBearerFromUser($patientUser->object());
        $client->request('GET', '/api/patients/hospital_stays');

        // Assert
        $this->assertResponseIsSuccessful();
        // se baser sur le nombre est déjà un indice pertinent
        $this->assertJsonContains([
            '@context' => '/api/contexts/HospitalStay',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);
    }

    public function testPatientCreatesAnHospitalStay(): void
    {
        // Arrange
        $patientUser = $this->makePatientUser();
        $patient = $patientUser->getPatient();
        $doctor = DoctorFactory::new()->create();

        // Act
        $client = static::createClientWithBearerFromUser($patientUser->object());
        $client->request(
            'POST',
            '/api/hospital_stays',
            [                'headers' => [
                'Content-Type' => 'application/ld+json',
                'Accept' => 'application/ld+json',
            ],
                'json' => [
                    'patient' => '/api/patients/' . $patient->getId(),
                    'startDate' => '2024-01-01',
                    'endDate' => '2024-01-05',
                    'reason' => 'Mal de tête',
                    'medicalSpeciality' => 'Neurologie',
                    'doctor' => '/api/doctors/'.$doctor->object()->getId(),
                ]]);

        // Assert
        $this->assertResponseIsSuccessful();
    }

    private function makePatientUser(): Proxy|User
    {
        $user = UserFactory::new()->create(
            [
                'email' => 'patient@patient.com',
                'password' => 'hello',
                'roles' => [],
                'access_token' => UserFactory::VALID_PATIENT_TOKEN,
                'token_expiration' => new DateTime('+30 day'),
            ]
        );
        PatientFactory::new()->create(
            [
                'user' => $user,
                'hospitalStays' => []

            ]);
        if(!in_array('ROLE_PATIENT', $user->object()->getRoles())) {
            throw new Exception('User Patient non associé à un patient');
        }

        return $user;
    }
}

