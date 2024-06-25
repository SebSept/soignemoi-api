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
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use HasBrowser;

    public function testCreatePatientSuccess(): void
    {
        $this->browser()
            ->request('POST', '/api/patients', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
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
        )
        ->assertSuccessful();

        // 2 entités créés
        UserFactory::assert()->count(1);
        PatientFactory::assert()->count(1);

        $user = UserFactory::repository()->first();
        $this->assertTrue($user->object()->isPatient());
//        $this->assertSame($patient->object(), $user->object()->getPatient());
//        $this->assertInstanceOf(User::class, $patient->object()->getUser());
    }

    public function testCreatePatientFailsOnInvalidEmail(): void
    {
        $this->browser()
            ->request('POST', '/api/patients', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'firstname' => 'newuser',
                'lastname' => 'Doe',
                'address1' => '1 rue de la paix',
                'address2' => '75000 Paris',
                // champs pour la création du user
                'userCreationEmail' => 'invalid',
                'userCreationPassword' => 'password-verx-y7-strang'
            ]
            ]
        )
        ->assertStatus(422);

        // aucune entité crée
        UserFactory::assert()->count(0);
        PatientFactory::assert()->count(0);
    }

    public function testCreatePatientFailsOnAlreadyUsedEmail(): void
    {
        UserFactory::new(['email' => 'user@email.com'])->create();

        $this->browser()
            ->request('POST', '/api/patients', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'firstname' => 'newuser',
                'lastname' => 'Doe',
                'address1' => '1 rue de la paix',
                'address2' => '75000 Paris',
                // champs pour la création du user
                'userCreationEmail' => 'user@email.com',
                'userCreationPassword' => 'password-verx-y7-strang'
            ]
            ]
        )
        ->assertStatus(422);

        // pas de nouveau user (reste celui créé pour le test)
        UserFactory::assert()->count(1);
        // aucune patient créé
        PatientFactory::assert()->count(0);
    }

    /**
     * Pour vérifier la validé du test,  on peut modifier le fichier src/ApiResource/StateProcessor/CreatePatient.php : \App\ApiResource\StateProcessor\CreatePatient::process
     * en y mettant :
     * // Example de code qui comporte une possibilité d'injection sql
     * $conn = $this->entityManager->getConnection();
     * $sql = "INSERT INTO \"user\" (password) VALUES ('" . $patient->userCreationPassword . "') LIMIT 1";
     * $stmt = $conn->prepare($sql);
     * $stmt->executeQuery();
     */
    public function testCreatePatientNotVulnerableToSQLBlindInjection(): void
    {
        UserFactory::new(['email' => 'user@email.com'])->create();

        $start = microtime(true);
        $this->browser()
            ->request('POST', '/api/patients', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    // error based
                    // 'body' => '{ "firstname": "string", "lastname": "string", "address1": "string", "address2": "string", "password": "string", "userCreationEmail": "user@example.com", "userCreationPassword": "password-verx-y7-strang\'||(SELECT (CHR(102)||CHR(70)||CHR(70)||CHR(90)) WHERE 8256=8256 AND 3022=CAST((CHR(113)||CHR(107)||CHR(120)||CHR(112)||CHR(113))||(SELECT (CASE WHEN (3022=3022) THEN 1 ELSE 0 END))::text||(CHR(113)||CHR(122)||CHR(113)||CHR(122)||CHR(113)) AS NUMERIC))||\'" }',

                    'body' => '{ "firstname": "string", "lastname": "string", "address1": "string", "address2": "string", "password": "string", "userCreationEmail": "user@example.com", "userCreationPassword": "password-verx-y7-strang\'||(SELECT (CHR(113)||CHR(119)||CHR(111)||CHR(103)) WHERE 2442=2442 AND 1753=(SELECT 1753 FROM PG_SLEEP(5)))||\'" }'
                ]
            );

        $this->assertLessThan(5, microtime(true) - $start, 'time based sql injection réussie.');
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
        $this->browser()
            ->actingAs($patientUser->object())
        ->request('GET', '/api/patients/hospital_stays')

        // Assert
        ->assertSuccessful()
        // se baser sur le nombre est déjà un indice pertinent
        ->assertJsonMatches(            '"@context"', '/api/contexts/HospitalStay')
            ->assertJsonMatches(            '"@type"' , 'hydra:Collection')
            ->assertJsonMatches(            '"hydra:totalItems"', 2)
        ;
    }

    public function testPatientCreatesAnHospitalStay(): void
    {
        // Arrange
        $patientUser = $this->makePatientUser();
        $patient = $patientUser->getPatient();
        $doctor = DoctorFactory::new()->create();

        // Act
        $this->browser()->actingAs($patientUser->object())
        ->post(
            '/api/hospital_stays',
                HttpOptions::json([
                'patient' => '/api/patients/' . $patient->getId(),
                'startDate' => '2024-01-01',
                'endDate' => '2024-01-05',
                'reason' => 'Mal de tête',
                'medicalSpeciality' => 'Neurologie',
                'doctor' => '/api/doctors/'.$doctor->object()->getId(),
                ]))

        // Assert
        ->assertSuccessful();
    }

//    public function testPatientCannotCreatesAnHospitalStayStartingBeforeTomorrow(): void
//    {
//        // Arrange
//        $patientUser = $this->makePatientUser();
//        $patient = $patientUser->getPatient();
//        $doctor = DoctorFactory::new()->create();
//
//        // Act
//        $client = static::createClientWithBearerFromUser($patientUser->object());
//        $client->request(
//            'POST',
//            '/api/hospital_stays',
//            [                'headers' => [
//                'Content-Type' => 'application/ld+json',
//                'Accept' => 'application/ld+json',
//            ],
//                'json' => [
//                    'patient' => '/api/patients/' . $patient->getId(),
//                    'startDate' => '2024-01-01',
//                    'endDate' => '2024-01-05',
//                    'reason' => 'Mal de tête',
//                    'medicalSpeciality' => 'Neurologie',
//                    'doctor' => '/api/doctors/'.$doctor->object()->getId(),
//                ]]);
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//    }

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

