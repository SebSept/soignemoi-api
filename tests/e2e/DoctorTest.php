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

    use prescriptions;

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

trait prescriptions {

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

    public function testCreatePrescriptionLimitedToOnePerPayPerPatient(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        $doctor = DoctorFactory::repository()->first()->object();
        PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctor,
        ]);

        // Act
        $patientIri = '/api/patients/' . $patient->getId();
        $doctorIri = '/api/doctors/' . $doctor->getId();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'items' => []
        ];
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/prescriptions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload
            ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'La création de cet objet est limitée à 1 par jour par patient et par docteur'
        ]);
    }

    public function testPatchExistingPrescription(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        $doctor = DoctorFactory::repository()->first()->object();
        $prescription = PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctor,
        ]);
        $prescriptionId = $prescription->getId();

        // Act
        $payload = [
            'items' => [
                [
                    'drug' => 'medicament',
                    'dosage' => '1g',
                ]
            ]
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/prescriptions/' . $prescriptionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        PrescriptionFactory::repository()->assert()->count(1);
    }

    public function testPatchExistingPrescriptionWithAnotherDoctor(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        $doctor = DoctorFactory::repository()->first()->object();
        $otherDoctor = DoctorFactory::new()->create();
        $prescription = PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctor, // docteur authentifié
        ]);
        $prescriptionId = $prescription->getId();

        // Act
        $payload = [
            'patient' => '/api/patients/' . $patient->getId(),
            'doctor' => '/api/doctors/' . $otherDoctor->getId(),
            // passer le champs 'items' vide cause une erreur
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/prescriptions/' . $prescriptionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload
            ]);

        // Assert
        // on a une réponse en 200, mais le docteur ne doit pas avoir changé
        // on le vérifie dans le contenu de la réponse
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'doctor' => '/api/doctors/'.$doctor->getId()
        ]);
    }
}