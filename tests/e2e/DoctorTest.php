<?php

namespace App\Tests\e2e;

use App\Factory\DoctorFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function testCanAccessIri(): void
    {
        $ids = $this->makeEntities();
        $user = $this->makeDoctorUser();

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

    /**
     * @x-dataProvider NotAllowedIris
     * pas possible d'utiliser un dataprovider (du moins, je n'ai pas réussi)
     */
    public function testCannotAccessIri()
    {
        $this->makeEntities();
        $user = $this->makeDoctorUser();

        foreach ($this->NotAllowedIris() as $iri) {
            $this->testAccessNotAllowedTo($iri[0], $user);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/prescriptions'],
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

    public function testCreatePrescription()
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $patientIri = '/api/patients/' . $patient->getId();
        $doctorUser = $this->makeDoctorUser();
        $doctor = DoctorFactory::repository()->first()->object(); // on devrait le retrouver avec le user->getDoctor() mais ça ne marche pas.
        $doctorIri = '/api/doctors/' . $doctor->getId();
        $nbPrescriptions = PrescriptionFactory::repository()->count();


        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'date' => '2021-09-01',
            'items' => []
        ];

        // Act
        // test accès aux uri des docteur et patient
//        PatientFactory::repository()->assert()->exists($patient);
//        DoctorFactory::repository()->assert()->exists($doctor);
//
//        static::createClientWithBearerFromUser($doctorUser->object())
//            ->request('GET', $patientIri);
//        $this->assertResponseIsSuccessful();
//        static::createClientWithBearerFromUser($doctorUser->object())
//            ->request('GET', $doctorIri);
//        $this->assertResponseIsSuccessful();

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/prescriptions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        PrescriptionFactory::repository()->assert()->count($nbPrescriptions+1);
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

    private function makeDoctorUser(): \Zenstruck\Foundry\Proxy|\App\Entity\User
    {
        return UserFactory::new()->doctor()->create();
    }
}
