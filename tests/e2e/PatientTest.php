<?php

namespace App\Tests\e2e;

use App\Entity\MedicalOpinion;
use App\Factory\HospitalStayFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function testCanAccessIri(): void
    {
        $ids = $this->makeEntities();

        foreach ($this->AllowedIris($ids) as $iri) {
            $this->testAccessOk($iri[0]);
        }
    }

    private function AllowedIris(array $ids): array
    {
        return [
            ['/api/hospital_stays'],
        ];
    }

    public function testCannotAccessIri()
    {
        $this->makeEntities();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0]);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/medical_opinions'],
        ];
    }

    private function testAccessOk(string $iri): void
    {
        $user = $this->makePatient();

        static::createClientWithBearerFromUser($user->object())
            ->request('GET', $iri);

        $this->assertResponseIsSuccessful(' raté pour ' . $iri);
    }

    private function testAccessNotAllowedTo(string $string): void
    {
        $user = $this->makePatient();

        static::createClientWithBearerFromUser($user->object())
            ->request('GET', $string);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @return array
     */
    private function makeEntities(): array
    {
        return [
//            'patientId' => PatientFactory::new()->create()->getId(),
//            'prescriptionId' => PrescriptionFactory::new()->create()->getId(),
//            'medicalOpinionId' => MedicalOpinionFactory::new()->create()->getId(),
        ];
    }

    private function makePatient(): \Zenstruck\Foundry\Proxy|\App\Entity\User
    {
        return UserFactory::new()->patient()->create();
    }
}
