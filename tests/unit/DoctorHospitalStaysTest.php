<?php

namespace App\Tests\unit;

use DateTime;
use App\Entity\HospitalStay;
use App\Factory\DoctorFactory;
use App\Factory\HospitalStayFactory;
use App\Factory\PatientFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DoctorHospitalStaysTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;
    public function testHospitalStaysRepositoryTodayHospitalStaysForDoctor(): void
    {
        $kernel = self::bootKernel();
        $hospitalStayRepository = $kernel->getContainer()->get('doctrine')->getRepository(HospitalStay::class);

        $doctor = DoctorFactory::new()->create();
        $otherDoctor = DoctorFactory::new()->create();
        $patient = PatientFactory::new()->create();
        $expected = 5; // pas un multiple de 2, on utilise 2 pour les données à ne pas renvoyer.

        // cas possibles : 3 * 2 docteurs
        // ∅ CI ∅ CO
        //   CI ∅ CO
        //   CI   CO

        // Ci ∅ Co Doc : 5 patients
        HospitalStayFactory::new(static fn(): array => [
            'doctor' => $doctor,
            'patient' => $patient,
            'checkin' => random_int(0, 1) !== 0 ? new DateTime('today') : new DateTime('yesterday'),
            'checkout' => null,
        ] )->many($expected)->create();

        // Ci ∅ Co2 xDoc : 2 patients
        HospitalStayFactory::new([
            'doctor' => $otherDoctor,
            'patient' => $patient,
            'checkin' => new DateTime('today'),
            'checkout' => null,
        ] )->many(2)->create();

        // Ci Co Doc : 2 patients
        HospitalStayFactory::new([
            'doctor' => $doctor,
            'patient' => $patient,
            'checkin' => new DateTime('yesterday'),
            'checkout' => new DateTime('today'),
        ] )->many(2)->create();

        // Ci Co xDoc : 2 patients
        HospitalStayFactory::new([
            'doctor' => $otherDoctor,
            'patient' => $patient,
            'checkin' => new DateTime('yesterday'),
            'checkout' => new DateTime('today'),
        ] )->many(2)->create();

        // ∅ Ci ∅ Co xDoc : 2 patients
        HospitalStayFactory::new([
            'doctor' => $otherDoctor,
            'patient' => $patient,
            'checkin' => null,
            'checkout' => null,
        ] )->many(2)->create();

        // ∅ Ci ∅ Co xDoc : 2 patients
        HospitalStayFactory::new([
            'doctor' => $doctor,
            'patient' => $patient,
            'checkin' => null,
            'checkout' => null,
        ] )->many(2)->create();

        $this->assertCount($expected, $hospitalStayRepository->findByDoctorForToday($doctor->getId()));
    }

    public function testHospitalStayGetPrescriptionsBetween(): void
    {
        // quelques prescriptions dans la base de données
        // @todo écrire ce test
//        PrescriptionFactory::new()->many(2,5)->create();
//
//        $patient = PatientFactory::new()->create();
//        HospitalStayFactory::new()->withPrescriptions(3)->create([
//            'patient' => $patient
//        ]);
    }
}
