<?php

namespace App\Tests\integration;

use App\Entity\Prescription;
use App\Factory\DoctorFactory;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;
    public function testGetTodayPrescriptionByDoctorReturnsAPrescription(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctor = DoctorFactory::new()->create();
        HospitalStayFactory::new()
            ->exitAfterToday()
            ->entryBeforeToday()
            ->create(                [
                'patient' => $patient,
                'doctor' => $doctor,

                ]
            );

        PrescriptionFactory::new()->create(                [
                'patient' => $patient,
                'doctor' => $doctor,
            ]
        );


        // Act
        // $patient = PatientFactory::repository()            ->find($patient->object());
        $foundPrescription = $patient->getTodayPrescriptionByDoctor($doctor->object());

        // Assert
        $this->assertInstanceOf(Prescription::class, $foundPrescription);
    }

    public function testGetTodayPrescriptionByDoctorReturnsNull(): void
    {
        // Arrange
        $patient = PatientFactory::new()->create();
        $doctor = DoctorFactory::new()->create();
        HospitalStayFactory::new()
            ->exitAfterToday()
            ->entryBeforeToday()
            ->create(                [
                'patient' => $patient,
                'doctor' => $doctor,

                ]
            );
        // no prescription added

        // Act
        $foundPrescription = $patient->getTodayPrescriptionByDoctor($doctor->object());

        // Assert
        $this->assertNull($foundPrescription);
    }
}
