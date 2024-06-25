<?php

namespace App\Tests\e2e;

use App\Factory\PatientFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientCreateTest extends ApiTestCase
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
                    'json' => $this->validPayload()
                ]
            )
            ->assertSuccessful();

        // 2 entités créés
        UserFactory::assert()->count(1);
        PatientFactory::assert()->count(1);

        $user = UserFactory::repository()->first();
        $this->assertTrue($user->object()->isPatient());
    }

    public function testCreatePatientFailsOnInvalidEmail(): void
    {
        $this->testCreationFails($this->validPayload(['userCreationEmail' => 'invalid']));
    }

    public function testCreatePatientFailsOnEmptyEmail(): void
    {
        $this->testCreationFails($this->validPayload(['userCreationEmail' => '']));
    }

    public function testCreatePatientFailsOnAlreadyUsedEmail(): void
    {
        $testEmail = 'user@email.com';
        UserFactory::new(['email' => $testEmail])->create();
        $this->testCreationFails($this->validPayload(['userCreationEmail' => $testEmail]));
    }

    public function testCreatePatientFailsOnTooWeakPassword(): void
    {
        $this->testCreationFails($this->validPayload([ 'userCreationPassword' => '1234567']));
    }

    public function testCreatePatientFailsOnEmptyPassword(): void
    {
        $this->testCreationFails($this->validPayload([ 'userCreationPassword' => '']));
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

    private function testCreationFails(array $payload): void
    {
        $usersCountsBefore = UserFactory::repository()->count();
        $patientsCountsBefore = PatientFactory::repository()->count();

        $this->browser()
            ->request('POST', '/api/patients', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => $payload
                ]
            )
            ->assertStatus(422);

        // aucune entité crée
        UserFactory::assert()->count($usersCountsBefore);
        PatientFactory::assert()->count($patientsCountsBefore);
    }

    /**
     * @return string[]
     * Renvoit le payload mergé avec les champs de modified
     */
    private function validPayload(array $modified = []): array
    {
        return
            $modified + [
                'firstname' => 'newuser',
                'lastname' => 'Doe',
                'address1' => '1 rue de la paix',
                'address2' => '75000 Paris',
                // champs pour la création du user
                'userCreationEmail' => 'pass@email.com',
                'userCreationPassword' => 'password-verx-y7-strang'
            ];
    }

}

