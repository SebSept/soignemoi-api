<?php

namespace App\Tests\e2e;

use App\Entity\MedicalOpinion;
use App\Factory\HospitalStayFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
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
        $ids = $this->makeEntities();
        $user = $this->makePatient();

        foreach ($this->AllowedIris($ids) as $iri) {
            $this->testAccessOk($iri[0], $user);
        }
    }

    private function AllowedIris(array $ids): array
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

    private function testAccessOk(string $iri, Proxy $user): void
    {
        static::createClientWithBearerFromUser($user->object())
            ->request('GET', $iri);

        $this->assertResponseIsSuccessful(' raté pour ' . $iri);
    }

    private function testAccessNotAllowedTo(string $string, Proxy $user): void
    {
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
