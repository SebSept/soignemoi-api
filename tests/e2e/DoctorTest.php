<?php

namespace App\Tests\e2e;

use Exception;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    use prescriptions;
    use medicalOpinions;

    public function testCanAccessIri(): void
    {
        $user = $this->makeDoctorUser();

        foreach ($this->AllowedIris() as $iri) {
            static::createClientWithBearerFromUser($user->object())
                ->request('GET', $iri[0]);

            $this->assertResponseIsSuccessful(' raté pour '.$iri[0]);
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

    public function testCanViewTodayHospitalStays(): void
    {
        // Arrange
        $doctorUser = $this->makeDoctorUser();
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('GET', '/api/doctors/'.$doctorUser->object()->getDoctor()->getId().'/hospital_stays/today', [
                'headers' => [
                    // 'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        // pas de vérif sur le compte, on à un test du repository : \App\Tests\unit\DoctorHospitalStaysTest
    }

    private function makeDoctorUser(): Proxy|User
    {
        $doctor = UserFactory::new()->doctor()->create();
        if(!in_array('ROLE_DOCTOR', $doctor->object()->getRoles())) {
            throw new Exception('User Doctor non associé à un docteur');
        }

        return $doctor;
    }
}

trait prescriptions
{
    public function testCreatePrescription(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $patientIri = '/api/patients/'.$patient->getId();
        $doctorUser = $this->makeDoctorUser();
        $doctorIri = '/api/doctors/'.$doctorUser->getDoctor()->getId();
        $nbPrescriptions = PrescriptionFactory::repository()->count();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'items' => [],
        ];

        // Act
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/prescriptions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
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
        PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctorUser->getDoctor(),
        ]);

        // Act
        $patientIri = '/api/patients/'.$patient->getId();
        $doctorIri = '/api/doctors/'.$doctorUser->getDoctor()->getId();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'items' => [],
        ];
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/prescriptions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'La création de cet objet est limitée à 1 par jour par patient et par docteur',
        ]);
    }

    public function testPatchExistingPrescription(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        $prescription = PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctorUser->getDoctor(),
        ]);
        $prescriptionId = $prescription->getId();

        // Act
        $payload = [
            'items' => [
                [
                    'drug' => 'medicament',
                    'dosage' => '1g',
                ],
            ],
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/prescriptions/'.$prescriptionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
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
        DoctorFactory::repository()->first()->object();
        $otherDoctor = DoctorFactory::new()->create();

        $prescription = PrescriptionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $otherDoctor,
        ]);
        $prescriptionId = $prescription->getId();

        // Act
        $payload = [
            // qu'importe les données, ces champs doctor et patient ne sont pas traités, pas dispo en écriture
            'patient' => '/api/patients/'.$patient->getId(),
            'doctor' => '/api/doctors/'.$otherDoctor->getId(),
            // passer le champs 'items' vide cause une erreur
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/prescriptions/'.$prescriptionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
    }
}

trait medicalOpinions
{
    public function testCreateMedicalOpinion(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $patientIri = '/api/patients/'.$patient->getId();
        $doctorUser = $this->makeDoctorUser();
        $doctorIri = '/api/doctors/'.$doctorUser->getDoctor()->getId();
        $nbMedicalOpinions = MedicalOpinionFactory::repository()->count();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'title' => 'une prescription',
            'description' => 'une description bla bla',
        ];

        // Act
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/medical_opinions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        MedicalOpinionFactory::repository()->assert()->count($nbMedicalOpinions + 1);
    }

    public function testCreateMedicalOpinionLimitedToOnePerPayPerPatient(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        MedicalOpinionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctorUser->getDoctor(),
        ]);

        // Act
        $patientIri = '/api/patients/'.$patient->getId();
        $doctorIri = '/api/doctors/'.$doctorUser->getDoctor()->getId();

        $payload = [
            'patient' => $patientIri,
            'doctor' => $doctorIri,
            'title' => 'un avis médical',
            'description' => 'une description bla bla',
        ];
        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('POST', '/api/medical_opinions', [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'La création de cet objet est limitée à 1 par jour par patient et par docteur',
        ]);
    }

    public function testPatchExistingMedicalOpinion(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        $medicalOpinion = MedicalOpinionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $doctorUser->getDoctor(),
        ]);
        $medicalOpinionId = $medicalOpinion->getId();

        // Act
        $payload = [
            'title' => 'un avis médical modifié',
            'description' => 'description modifiée',
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/medical_opinions/'.$medicalOpinionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'un avis médical modifié',
            'description' => 'description modifiée',
        ]);
        MedicalOpinionFactory::repository()->assert()->count(1);
    }

    public function testPatchExistingMedicalOpinionWithAnotherDoctor(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctorUser = $this->makeDoctorUser();
        DoctorFactory::repository()->first()->object();
        $otherDoctor = DoctorFactory::new()->create();
        $medicalOpinion = MedicalOpinionFactory::new()->create([
            'patient' => $patient,
            'doctor' => $otherDoctor,
        ]);
        $medicalOpinionId = $medicalOpinion->getId();

        // Act
        $payload = [
            'patient' => '/api/patients/'.$patient->getId(),
            'doctor' => '/api/doctors/'.$otherDoctor->getId(),
        ];

        $client = static::createClientWithBearerFromUser($doctorUser->object());
        $client
            ->request('PATCH', '/api/medical_opinions/'.$medicalOpinionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
    }
}
