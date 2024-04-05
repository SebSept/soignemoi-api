<?php

namespace App\Tests\unit;

use App\Factory\DoctorFactory;
use App\Factory\PatientFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PrescriptionTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    public function testTheDateIsCreatedWithTheCurrentDate(): void
    {
        $prescription = PrescriptionFactory::new()->create([
            'doctor' => DoctorFactory::new()->create(),
            'patient' => PatientFactory::new()->new()->create(),
            'items' => [],
        ]);


        $this->assertEquals(
            $prescription->getDate()->format("Y-m-d"),
            (new \DateTime('now'))->format("Y-m-d")
        );
    }
}