<?php

namespace App\Tests\e2e;

use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AdminTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    use HospitalStays;

    public function testModifyAnHospitalStay(): void
    {
        $this->modifyAnHospitalStay($this->makeAdmin()->object());
    }

    public function testUpdateDoctor(): void
    {
        $admin = $this->makeAdmin();
        $doctor = DoctorFactory::new()->create();

        $client = static::createClientWithBearerFromUser($admin->object());
        $client->request('PATCH', '/api/doctors/' . $doctor->getId(), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/ld+json',
            ],
            'json' => [
                'firstname' => 'mALLICK',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstname' => 'mALLICK',
        ]);
    }

    public function testCreateDoctor(): void
    {
        $admin = $this->makeAdmin();
        DoctorFactory::new()->create();

        $client = static::createClientWithBearerFromUser($admin->object());
        $client->request('POST', '/api/doctors' , [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Accept' => 'application/ld+json',
            ],
            'json' => [
                'firstname' => 'mALLICK',
                'lastname' => 'Doe',
                'medicalSpeciality' => 'Généraliste',
                'employeeId' => '123',
                'password' => 'password-verx-y7-strang',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstname' => 'mALLICK',
            'employeeId' => '123',
        ]);
    }

    public function testCanAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makeAdmin();

        foreach ($this->AllowedIris() as $iri) {
            $this->testAccessOk($iri[0], $user);
        }
    }

    private function AllowedIris(): array
    {
        return [
            ['/api/hospital_stays'],
            ['/api/doctors'],
        ];
    }

    public function testCannotAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makeAdmin();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $user);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/medical_opinions'],
            ['/api/patients'],
        ];
    }

    private function testAccessOk(string $iri, Proxy $proxy): void
    {
        static::createClientWithBearerFromUser($proxy->object())
            ->request('GET', $iri);

        $this->assertResponseIsSuccessful(' raté pour ' . $iri);
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

    private function makeAdmin(): Proxy|User
    {
        return UserFactory::new()->admin()->create();
    }
}
