<?php

namespace App\Tests\e2e;

use Zenstruck\Foundry\Proxy;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCanAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makeDoctorUser();

        foreach ($this->AllowedIris() as $iri) {
            static::createClientWithBearerFromUser($user->object())
                ->request('GET', $iri[0]);

            $this->assertResponseIsSuccessful(' raté pour ' . $iri[0]);
        }
    }

    private function AllowedIris(): array
    {
        return [
            ['/api/hospital_stays'],
        ];
    }

    /**
     * @x-dataProvider NotAllowedIris
     * pas possible d'utiliser un dataprovider (du moins, je n'ai pas réussi)
     */
    public function testCannotAccessIri(): void
    {
        $this->makeEntities();
        $user = $this->makeDoctorUser();

        foreach ($this->NotAllowedIris() as $iri) {
            static::createClientWithBearerFromUser($user->object())
                ->request('GET', $iri[0]);

            $this->assertResponseStatusCodeSame(403);
        }
    }

    private function NotAllowedIris(): array
    {
        return [
            ['/api/prescriptions'],
        ];
    }

    public function testCreatePrescription(): void
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
            'items' => []
        ];

        // Act
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
        PrescriptionFactory::repository()->assert()->count($nbPrescriptions + 1);
    }

    public function testCreateMedicalOpinion(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $patientIri = '/api/patients/' . $patient->getId();
        $doctorUser = $this->makeDoctorUser();
        $doctor = DoctorFactory::repository()->first()->object();
        $doctorIri = '/api/doctors/' . $doctor->getId();
        $nbMedicalOpinions = MedicalOpinionFactory::repository()->count();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'title' => 'une prescription',
            'description' => 'une description bla bla',
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
            ->request('POST', '/api/medical_opinions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        MedicalOpinionFactory::repository()->assert()->count($nbMedicalOpinions + 1);
    }

    private function makeEntities(): array
    {
        return [
//            'patientId' => PatientFactory::new()->create()->getId(),
//            'prescriptionId' => PrescriptionFactory::new()->create()->getId(),
//            'medicalOpinionId' => MedicalOpinionFactory::new()->create()->getId(),
        ];
    }

    private function makeDoctorUser(): Proxy|User
    {
        return UserFactory::new()->doctor()->create();
    }
}
