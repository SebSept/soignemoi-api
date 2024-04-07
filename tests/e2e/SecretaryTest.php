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

class SecretaryTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCanAccessIri(): void
    {
        $ids = $this->makeEntities();
        $user = $this->makeSecretary();

        foreach ($this->AllowedIris($ids) as $iri) {
            $this->testAccessOk($iri[0], $user);
        }
    }

    /**
     * @x-dataProvider NotAllowedIris
     * pas possible d'utiliser un dataprovider (du moins, je n'ai pas réussi)
     */
    public function testCannotAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makeSecretary();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $user);
        }
    }

    public function testCountTodayEntries(): void
    {
        // Arrange
        HospitalStayFactory::new()->entryBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->entryToday()->many(5)->create();
        HospitalStayFactory::new()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->entryAfterToday()->many(3)->create();

        // Act
        $secretary = UserFactory::new()->secretary()->create();
        static::createClientWithBearerFromUser($secretary->object())
            ->request('GET', '/api/hospital_stays/today_entries');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 5]);
    }

    public function testCountTodayExits(): void
    {
        // Arrange
        HospitalStayFactory::new()->exitBeforeToday()->many(3)->create();
        HospitalStayFactory::new()->exitToday()->many(2)->create();
        HospitalStayFactory::new()->entryToday()->many(2)->create();
        HospitalStayFactory::new()->exitAfterToday()->many(3)->create();

        // Act
        $secretary = UserFactory::new()->secretary()->create();
        static::createClientWithBearerFromUser($secretary->object())
            ->request('GET', '/api/hospital_stays/today_entries');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    private function AllowedIris(array $ids): array
    {
        return [
            ['/api/hospital_stays/today_entries'],
            ['/api/hospital_stays/today_exits'],
            ['/api/patients/'.$ids['patientId']],
            ['/api/prescriptions'],
            ['/api/prescriptions/'.$ids['prescriptionId']],
            ['/api/medical_opinions'],
            ['/api/medical_opinions/'.$ids['medicalOpinionId' ]],
        ];
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/doctors'],
            ['/api/patients'],
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
     * @return array{patientId: int, prescriptionId: int, medicalOpinionId: int}
     */
    private function makeEntities(): array
    {
        return [
            'patientId' => PatientFactory::new()->create()->getId(),
            'prescriptionId' => PrescriptionFactory::new()->create()->getId(),
            'medicalOpinionId' => MedicalOpinionFactory::new()->create()->getId(),
        ];
    }

    private function makeSecretary(): Proxy
    {
        return UserFactory::new()->secretary()->create();
    }
}
