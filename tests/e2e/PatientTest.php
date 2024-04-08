<?php

namespace App\Tests\e2e;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCanAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makePatient();

        foreach ($this->AllowedIris() as $iri) {
            $this->testAccessOk($iri[0], $user);
        }
    }

    private function AllowedIris(): array
    {
        return [
            ['/api/hospital_stays'],
        ];
    }

    public function testCannotAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makePatient();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $user);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/medical_opinions'],
        ];
    }

    private function testAccessOk(string $iri, Proxy $proxy): void
    {
        static::createClientWithBearerFromUser($proxy->object())
            ->request('GET', $iri);

        $this->assertResponseIsSuccessful(' raté pour '.$iri);
    }

    private function testAccessNotAllowedTo(string $string, Proxy $proxy): void
    {
        static::createClientWithBearerFromUser($proxy->object())
            ->request('GET', $string);

        $this->assertResponseStatusCodeSame(403);
    }

    private function makeEntities(): array
    {
        return [
            //            'patientId' => PatientFactory::new()->create()->getId(),
            //            'prescriptionId' => PrescriptionFactory::new()->create()->getId(),
            //            'medicalOpinionId' => MedicalOpinionFactory::new()->create()->getId(),
        ];
    }

    private function makePatient(): Proxy|User
    {
        return UserFactory::new()->patient()->create();
    }
}
