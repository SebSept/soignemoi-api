<?php

namespace App\Tests\e2e;

use App\Entity\User;
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

    public function testPatientViewItsHospitalStays(): void
    {
        // Arrange - 2 patients avec des hospital_stays chacun
        $patientUser = $this->makePatient();
        $patient = PatientFactory::repository()->first();
        HospitalStayFactory::new()->createMany(2, ['patient' => $patient->object()]);

        $otherPatient = PatientFactory::new()->create();
        HospitalStayFactory::new()->createMany(3, ['patient' => $otherPatient->object()]);

        // Act
        $client = static::createClientWithBearerFromUser($patientUser->object());
        $client->request('GET', '/api/patients/'.$patient->object()->getId().'/hospital_stays/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/HospitalStay',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
        ]);
    }

    private function makePatient(): Proxy|User
    {
        return UserFactory::new()->patient()->create();
    }
}
