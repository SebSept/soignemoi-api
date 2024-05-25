<?php

declare(strict_types=1);


namespace App\Tests\e2e;


use DateTime;
use App\Entity\User;
use App\Factory\DoctorFactory;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;

trait HospitalStays
{
    use HasBrowser;

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

        $this->browser()->actingAs($actor)
            ->patch(
                '/api/hospital_stays/'.$medicalOpinionId,
                HttpOptions::create(['headers' =>
                [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept' => 'application/ld+json'
                ],
                'json' => $payload])
            )
            // Assert
            ->assertSuccessful()
            ->assertJsonMatches('checkin', '2024-04-10T08:00:00+00:00');
    }
}