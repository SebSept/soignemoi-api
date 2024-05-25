<?php

namespace App\Tests\e2e;

use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\MedicalOpinionFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use HasBrowser;

    use prescriptions;
    use medicalOpinions;

    public function testCanAccessIri(): void
    {
        $user = $this->makeDoctorUser();

        foreach ($this->AllowedIris() as $iri) {
            $this->browser()->actingAs($user->object())
                ->get($iri[0])
                ->assertSuccessful();
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
            $this->browser()->actingAs($user->object())
                ->get($iri[0])
                ->assertStatus(Response::HTTP_FORBIDDEN);
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
        $this->browser()->actingAs($doctorUser->object())
            ->get('/api/doctors/'.$doctorUser->object()->getDoctor()->getId().'/hospital_stays/today',HttpOptions::json())
            ->assertSuccessful();
        // pas de vérif sur le compte, on à un test du repository : \App\Tests\unit\DoctorHospitalStaysTest
    }

    private function makeDoctorUser(): Proxy|User
    {
        $doctor = UserFactory::new()->doctor()->create();
        assert(in_array('ROLE_DOCTOR', $doctor->object()->getRoles()), 'User Doctor non associé à un docteur');

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
        $this->browser()->actingAs($doctorUser->object())
            ->post('/api/prescriptions', HttpOptions::json($payload))
            ->assertSuccessful();

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
        $this->browser()->actingAs($doctorUser->object())
            ->post('/api/prescriptions', HttpOptions::json($payload))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('detail' ,  'La création de cet objet est limitée à 1 par jour par patient et par docteur',
            );
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

        $this->browser()->actingAs($doctorUser->object())
            ->patch(
                '/api/prescriptions/'.$prescriptionId,
                HttpOptions::create(['headers' =>
                    [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json'
                    ],
                    'json' => $payload])
            )
        // Assert
            ->assertSuccessful();

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

        $this->browser()->actingAs($doctorUser->object())
            ->patch(
                '/api/prescriptions/'.$prescriptionId,
                HttpOptions::create(['headers' =>
                    [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json'
                    ],
                    'json' => $payload])
            )
        // Assert
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        $this->browser()->actingAs($doctorUser->object())->post('/api/medical_opinions', HttpOptions::json($payload))

        // Assert
        ->assertSuccessful();

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

        $this->browser()->actingAs($doctorUser->object())
            ->post('/api/medical_opinions', HttpOptions::json($payload))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('detail' ,  'La création de cet objet est limitée à 1 par jour par patient et par docteur',
            );
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

        $this->browser()->actingAs($doctorUser->object())
            ->patch(
                '/api/medical_opinions/'.$medicalOpinionId,
                HttpOptions::create(['headers' =>
                    [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json'
                    ],
                    'json' => $payload])
            )
            // Assert
            ->assertSuccessful()
            ->assertJsonMatches('title', 'un avis médical modifié')
            ->assertJsonMatches('description', 'description modifiée');

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

        $this->browser()->actingAs($doctorUser->object())
            ->patch(
                '/api/medical_opinions/'.$medicalOpinionId,
                HttpOptions::create(['headers' =>
                    [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json'
                    ],
                    'json' => $payload])
            )
            // Assert
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
