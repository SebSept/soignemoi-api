<?php

// tests/Validator/ContainsAlphanumericValidatorTest.php
namespace App\Tests\Validator;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use App\Validator\PrescriptionsLimiter;
use App\Validator\PrescriptionsLimiterValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PrescriptionLimiterValidator2Test extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        $existingPrescription = new Prescription();
        $mockRepository = $this->createMock(PrescriptionRepository::class);
        $mockRepository->method('findOneBy')->willReturn($existingPrescription);

        return new PrescriptionsLimiterValidator($mockRepository);
    }

    public function testPrescriptionExists(): void
    {
        $testedPrescription = new Prescription();
        $testedPrescription->setPatient(new Patient());
        $testedPrescription->setDoctor(new Doctor());

        $this->validator->validate($testedPrescription, new PrescriptionsLimiter());
        $this->buildViolation('La création de cet objet est limitée à 1 par jour par patient et par docteur')
            ->assertRaised();
    }

}