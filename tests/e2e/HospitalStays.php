<?php

declare(strict_types=1);


namespace App\Tests\e2e;


use DateTime;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;

trait HospitalStays
{
    public function modifyAnHospitalStay(User $actor): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctor = DoctorFactory::new()->create();
        $medicalOpinion = HospitalStayFactory::new()->create([
            'startDate' => new DateTime('2024-04-10'),
            'endDate' => new DateTime('2024-04-15'),
            'reason' => 'Toursime médical',
            'medicalSpeciality' => 'Généraliste',
            'patient' => $patient,
            'doctor' => $doctor,
        ]);
        $medicalOpinionId = $medicalOpinion->getId();

        // Act
        $payload = [
            'checkin' => '2024-04-10 08:00:00',
        ];

        $client = static::createClientWithBearerFromUser($actor);
        $client
            ->request('PATCH', '/api/hospital_stays/'.$medicalOpinionId, [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => $payload,
            ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'checkin' => '2024-04-10T08:00:00+00:00',
        ]);
    }
}